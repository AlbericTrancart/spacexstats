<?php
namespace SpaceXStats\Models\Traits;

use AWS;
use Aws\Exception\MultipartUploadException;
use Aws\S3\MultipartUploader;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use SpaceXStats\Library\Enums\VisibilityStatus;
use SpaceXStats\Models\Interfaces\UploadableInterface;

/**
 * Class UploadableTrait
 * @package SpaceXStats\Models\Traits
 */
trait UploadableTrait {

    /**
     * @var int
     */
    private $multipartUploadThreshold = 16000000; // 16MB

    /**
     * Checks whether the object has a file or not.
     *
     * This function does not care about the file's location, the object's status, or its visibility.
     *
     * @return bool
     */
    public function hasFile() {
        return $this->has_temporary_file || $this->has_local_file || $this->has_cloud_file;
    }

    /**
     * Checks whether the object has custom thumbnails or not.
     *
     * This function does not care about the thumb's location, the object's status, or its visibility.
     * If a file has a generic thumbnail (such as a "document" thumb), this function will return false.
     *
     * @return bool
     */
    public function hasThumbs() {
        return $this->has_temporary_thumbs || $this->has_local_thumbs || $this->has_cloud_thumbs;
    }

    /**
     * Checks whether the object's file is located in the cloud or not.
     *
     * This function does not check thumbnails.
     *
     * @return bool
     */
    public function hasCloudFile() {
        return $this->has_cloud_file;
    }

    /**
     * Checks whether the object has a copy of the full file stored locally.
     *
     * This function does not care about thumbnails, the object's status or its visibility.
     *
     * @return bool
     */
    public function hasLocalFile() {
        return $this->has_local_file;
    }

    /**
     * Checks whether the object has a copy of the full file stored locally in a temporary file.
     *
     * This function does not care about thumbnails, the object's status or its visibility.
     *
     * @return bool
     */
    public function hasTemporaryFile() {
        return $this->has_temporary_file;
    }

    /**
     * Checks whether the object has its own unique thumbnail or not stored in the cloud.
     *
     * This function will return false if the object has a generic thumbnail or does not have a thumbnail. It does
     * not care about the thumbnail's location, the object's status, or its visibility.
     *
     * @return bool
     */
    public function hasCloudThumbs() {
        return $this->has_cloud_thumbs;
    }

    /**
     * Checks whether the object has its own unique thumbnail or not stored locally.
     *
     * This function will return false if the object has a generic thumbnail or does not have a thumbnail. It does
     * not care about the thumbnail's location, the object's status, or its visibility.
     *
     * @return bool
     */
    public function hasLocalThumbs() {
        $defaultThumbs = array("audio.png", "document.png", "text.png", "comment.png", "article.png", "pressrelease.png", "tweet.png");
        return $this->has_local_thumbs && !in_array($this->thumb_filename, $defaultThumbs);
    }

    /**
     * Checks whether the object has its own unique thumbnail or not stored locally, in the temporary folder.
     *
     * This function will return false if the object has a generic thumbnail or does not have a thumbnail. It does
     * not care about the thumbnail's location, the object's status, or its visibility.
     *
     * @return bool
     */
    public function hasTemporaryThumbs() {
        return $this->has_temporary_thumbs;
    }

    /**
     * Checks whether the object has thumbnails which are generic
     *
     * @return bool
     */
    public function hasGenericThumbs() {

    }

    /**
     *  Uploads the objects file and thumbnails, if they exist, to Amazon S3 from the temporary storage location.
     *  Does not delete any temporary files, call deleteFromTemporary() for this.
     *
     *  Preferentially uses Amazon's Multipart Upload functionality when the file size exceeds 100MB.
     */
    public function putToCloud() {
        $s3 = AWS::createClient('s3');

        if ($this->hasFile()) {

            if ($this->exceedsMultipartUploadThreshold()) {
                $uploader = new MultipartUploader($s3, public_path($this->media), [
                    'Bucket' => Config::get('filesystems.disks.s3.bucket'),
                    'Key' => $this->filename,
                    'ACL' => $this->visibility === VisibilityStatus::PublicStatus ? 'public-read' : 'private'
                ]);

                try {
                    $uploader->upload();
                } catch (MultipartUploadException $e) {
                    Log::error('Item was not uploaded', ['message' => $e->getMessage() ]);
                }


            } else {
                $s3->putObject([
                    'Bucket' => Config::get('filesystems.disks.s3.bucket'),
                    'Key' => $this->filename,
                    'Body' => file_get_contents(public_path($this->media)),
                    'ACL' =>  $this->visibility === VisibilityStatus::PublicStatus ? 'public-read' : 'private',
                ]);
            }

            $this->has_cloud_file = true;
        }

        if ($this->hasThumbs()) {
            $s3->putObject([
                'Bucket' => Config::get('filesystems.disks.s3.bucketLargeThumbs'),
                'Key' => $this->thumb_filename,
                'Body' => file_get_contents(public_path($this->media_thumb_large)),
                'ACL' => $this->visibility === VisibilityStatus::PublicStatus ? 'public-read' : 'private',
                'StorageClass' => 'REDUCED_REDUNDANCY'
            ]);

            $s3->putObject([
                'Bucket' => Config::get('filesystems.disks.s3.bucketSmallThumbs'),
                'Key' => $this->thumb_filename,
                'Body' => file_get_contents(public_path($this->media_thumb_small)),
                'ACL' => 'public-read',
                'StorageClass' => 'REDUCED_REDUNDANCY'
            ]);
            $this->has_cloud_thumbs = true;
        }

        $this->reindex();

        return $this;
    }

    /**
     * Deletes all files, including thumbnails, from S3, if they exist.
     */
    public function deleteFromCloud() {
        $s3 = AWS::createClient('s3');

        if ($this->hasCloudFile()) {
            $s3->deleteObject(Config::get('filesystems.disks.s3.bucket'), $this->filename);
        }

        if ($this->hasCloudThumbs()) {
            $s3->deleteObject(Config::get('filesystems.disks.s3.bucketLargeThumbs'), $this->thumb_filename);
            $s3->deleteObject(Config::get('filesystems.disks.s3.bucketSmallThumbs'), $this->thumb_filename);
        }

        $this->has_cloud_file = false;
        $this->has_cloud_thumbs = false;

        return $this;
    }

    /**
     * Makes a local copy of a file and thumbs for an object, preferentially fetching from the temporary storage
     * before trying to fetch from S3. Does not delete any temporary files if they exist,
     * call deleteFromTemporary() for this. Does not save file locations to the database, call save() for this.
     */
    public function putToLocal() {

        $s3 = AWS::createClient('s3');

        if ($this->hasFile()) {
            if ($this->hasTemporaryFile()) {

                create_if_does_not_exist(public_path('media/local/full'));
                copy(public_path('media/temporary/full/' . $this->filename), public_path('media/local/full/' . $this->filename));

            } else if ($this->hasCloudFile()) {
                $s3->getObject(array(
                    'Bucket'    => Config::get('filesystems.disks.s3.bucket'),
                    'Key'       => $this->filename,
                    'SaveAs'    => public_path('media/local/' . $this->filename)
                ));
            }
            $this->has_local_file = true;
        }

        if ($this->hasThumbs()) {
            if ($this->hasTemporaryThumbs()) {

                create_if_does_not_exist(public_path('media/temporary/small/'));
                copy(public_path('media/temporary/small/' . $this->thumb_filename), public_path('media/local/small/' . $this->thumb_filename));

                create_if_does_not_exist(public_path('media/local/large/'));
                copy(public_path('media/temporary/large/' . $this->thumb_filename), public_path('media/local/large/' . $this->thumb_filename));

            } else if ($this->hasCloudThumbs()) {
                $s3->getObject(array(
                    'Bucket'    => Config::get('filesystems.disks.s3.bucketLargeThumbs'),
                    'Key'       => $this->thumb_filename,
                    'SaveAs'    => public_path('media/local/large/' . $this->filename)
                ));

                $s3->getObject(array(
                    'Bucket'    => Config::get('filesystems.disks.s3.bucketSmallThumbs'),
                    'Key'       => $this->thumb_filename,
                    'SaveAs'    => public_path('media/local/small/' . $this->filename)
                ));
            }
            $this->has_local_thumbs = true;
        }

        $this->reindex();

        return $this;
    }

    /**
     * Deletes the current local file and any thumbs from the local storage.
     */
    public function deleteFromLocal() {
        if ($this->hasLocalFile()) {
            unlink(public_path('media/local/full/' . $this->filename));
            $this->has_local_file = false;
        }

        if ($this->hasLocalThumbs()) {
            unlink(public_path('media/local/small/' . $this->thumb_filename));
            unlink(public_path('media/local/large/' . $this->thumb_filename));
            $this->has_local_thumbs = false;
        }

        return $this;
    }

    /**
     * Deletes any current temporary files
     *
     * @returns UploadableInterface
     */
    public function deleteFromTemporary() {
        if ($this->hasTemporaryFile()) {
            unlink(public_path('media/temporary/full/' . $this->filename));
            $this->has_temporary_file = false;
        }

        if ($this->hasTemporaryThumbs()) {
            unlink(public_path('media/temporary/small/' . $this->thumb_filename));
            unlink(public_path('media/temporary/large/' . $this->thumb_filename));
            $this->has_temporary_thumbs = false;
        }

        return $this;
    }

    /**
     * Checks to see if a multipart upload implementation should be used by comparing the file size of the to-be-uploaded file
     * against the upload threashold.
     *
     * @return bool
     */
    private function exceedsMultipartUploadThreshold() {
        return $this->size > $this->multipartUploadThreshold;
    }

    private function reindex() {
        Redis::sadd('objects:toReindex', $this->object_id);
    }
}
