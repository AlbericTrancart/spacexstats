<?php
namespace SpaceXStats\Services;

use SpaceXStats\Library\Enums\DateSpecificity;
use SpaceXStats\Library\Enums\MissionControlSubtype;
use SpaceXStats\Library\Enums\MissionControlType;
use SpaceXStats\Models\Object;

class DeltaVCalculator {

    const DELTAV_TO_DAY_CONVERSION_RATE     = 1000;
    const SECONDS_PER_DAY                   = 86400;

    protected $object;
    protected $score = 0;

    protected $baseTypeScores = [
        MissionControlType::Image       => 10,
        MissionControlType::GIF         => 10,
        MissionControlType::Audio       => 20,
        MissionControlType::Video       => 20,
        MissionControlType::Document    => 20,
        MissionControlType::Tweet       => 5,
        MissionControlType::Article     => 10,
        MissionControlType::Comment     => 5,
        MissionControlType::Webpage     => 10,
        MissionControlType::Text        => 10
    ];

    protected $specialTypeMultiplier = [
        MissionControlSubtype::MissionPatch     => 2,
        MissionControlSubtype::Photo            => 1.1,
        MissionControlSubtype::LaunchVideo      => 2,
        MissionControlSubtype::PressKit         => 2,
        MissionControlSubtype::WeatherForecast  => 2,
        MissionControlSubtype::PressConference  => 1.5
    ];

    protected $submissionQuality = [
        'multipliers' => [
            'perMegapixel' => 5,
            'perMinute' => 2
        ],
        'scores' => [
            'perPage' => 2
        ]
    ];

    protected $metadataScore = [
        'summary' => [
            'perCharacter' => 0.02
        ],
        'author' => [
            'perCharacter' => 0.2
        ],
        'attribution' => [
            'perCharacter' => 0.1
        ],
        'tags' => [
            'perTag' => 1
        ]
    ];

    protected $dateAccuracyMultiplier = [
        'Year' => 1,
        'Month' => 1.05,
        'Date' => 1.1,
        'Datetime' => 1.2,
    ];

    protected $dataSaverMultiplier = [
        'hasExternalUrl' => 2
    ];

    protected $originalContentMultiplier = [
        'isOriginalContent' => 1.5
    ];

    /**
     * Calculates the total deltaV value of a particular object
     *
     * @param   $object   SpaceXStats\Models\Object   The object to calculate the deltaV for.
     * @return  int                                     The total worth of the object in deltaV.
     */
    public function calculate(Object $object) {
        $this->score = 0; // Reset
        $this->object = $object;

        $this->typeRegime();
        $this->submissionQualityRegime();
        $this->metadataRegime();
        $this->dateAccuracyRegime();
        $this->dataSaverRegime();
        $this->originalContentRegime();

        return round($this->score);
    }

    /**
     * For a given amount of deltaV, calculates the number of seconds mission control it is worth.
     *
     * @param   $deltaV   int   The input value of deltaV.
     * @return  int             The number of seconds of mission control the input deltaV value corresponds to.
     */
    public function toSeconds($deltaV) {
        // Currently 86.4 seconds per point
        $secondsPerPoint = self::SECONDS_PER_DAY / self::DELTAV_TO_DAY_CONVERSION_RATE;

        return (int) round($deltaV * $secondsPerPoint);
    }

    /**
     * @internal
     */
    private function typeRegime() {
        // The base score
        $this->score += $this->baseTypeScores[$this->object->type];

        // The special type multiplier
        if (array_key_exists($this->object->subtype, $this->specialTypeMultiplier)) {
            $this->score *= $this->specialTypeMultiplier[$this->object->subtype];
        }
    }

    /**
     * @internal
     */
    private function submissionQualityRegime() {
        $submissionQualityScore = 0;

        if ($this->object->type == MissionControlType::Image) {
            $submissionQualityScore = $this->megapixelSubscore();
        }

        if ($this->object->type == MissionControlType::GIF || $this->object->type == MissionControlType::Video) {
            $submissionQualityScore = $this->megapixelSubscore() * $this->minuteSubscore();
        }

        if ($this->object->type == MissionControlType::Audio) {
            $submissionQualityScore = $this->minuteSubscore();
        }

        if ($this->object->type == MissionControlType::Document) {
            $submissionQualityScore = $this->pageSubscore();
        }

        $this->score += $submissionQualityScore;
    }

    /**
     * @internal
     */
    private function metadataRegime() {
        $this->score += strlen($this->object->summary) * $this->metadataScore['summary']['perCharacter'];
        $this->score += strlen($this->object->author) * $this->metadataScore['author']['perCharacter'];
        $this->score += strlen($this->object->attribution) * $this->metadataScore['attribution']['perCharacter'];
        $this->score += $this->object->tags()->count() * $this->metadataScore['tags']['perTag'];
    }

    /**
     * @internal
     */
    private function dateAccuracyRegime() {

        $this->score *= $this->dateAccuracyMultiplier[$this->object->originated_at_specificity];

        $year = substr($this->object->originated_at, 0, 4);
        $month = substr($this->object->originated_at, 5, 2);
        $day = substr($this->object->originated_at, 8, 2);
        $datetime = substr($this->object->originated_at, 11, 8);

        if ($datetime !== '00:00:00' || $datetime !== false) {
            $this->score *= $this->dateAccuracyMultiplier['Datetime'];
        } elseif ($day !== '00') {
            $this->score *= $this->dateAccuracyMultiplier['Day'];
        } elseif ($month !== '00') {
            $this->score *= $this->dateAccuracyMultiplier['Month'];
        } elseif ($year !== '0000') {
            $this->score *= $this->dateAccuracyMultiplier['Year'];
        }
    }

    /**
     * @internal
     * @return int
     */
    private function dataSaverRegime() {
        if (!is_null($this->object->external_url)) {
            return $this->score * $this->dataSaverMultiplier['hasExternalUrl'];
        }
    }

    /**
     * @internal
     * @return int
     */
    private function originalContentRegime() {
        if (!is_null($this->object->original_content)) {
            return $this->score * $this->originalContentMultiplier['isOriginalContent'];
        }
    }

    /**
     * @internal
     * @return mixed
     */
    private function megapixelSubscore() {
        $megapixels = ($this->object->dimension_width * $this->object->dimension_height) / 1000000;
        return $this->submissionQuality['multipliers']['perMegapixel'] * $megapixels;
    }

    /**
     * @internal
     * @return mixed
     */
    private function minuteSubscore() {
        return $this->submissionQuality['multipliers']['perMinute'] * ($this->object->duration / 60);
    }

    /**
     * @internal
     * @return mixed
     */
    private function pageSubscore() {
        return $this->submissionQuality['scores']['perPage'] * $this->object->page_count;
    }
}
