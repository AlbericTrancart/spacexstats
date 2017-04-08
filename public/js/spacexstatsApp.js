(function() {
    var app = angular.module('app', []);

    app.service('missionDataService', ["$http", function($http) {
        this.telemetry = function(slug) {
            return $http.get('/missions/'+ slug + '/telemetry');
        };

        this.orbitalElements = function(slug) {
            return $http.get('/missions/' + slug + '/orbitalelements').then(function(response) {
                // premap the dates of the timestamps because otherwise we'll do it too many times
                if (response.data === Array) {
                    return response.data.map(function(orbitalElement) {
                        orbitalElement.epoch = moment(orbitalElement.epoch).toDate();
                        return orbitalElement;
                    });
                }
            });
        };

        this.launchEvents = function(slug) {
            return $http.get('/missions/' + slug + '/launchevents');
        }
    }]);
})();

(function() {
    var app = angular.module('app', []);

    app.service('flashMessage', function() {
        this.addOK = function(message) {

            computeStayTime(message);

            $('<p style="display:none;" class="flash-message success">' + message + '</p>').appendTo('#flash-message-container').slideDown(300);

            setTimeout(function() {
                $('.flash-message').slideUp(300, function() {
                    $(this).remove();
                });
            }, computeStayTime());
        };

        this.addError = function(message) {

            computeStayTime(message);

            $('<p style="display:none;" class="flash-message failure">' + message + '</p>').appendTo('#flash-message-container').slideDown(300);

            setTimeout(function() {
                $('.flash-message').slideUp(300, function() {
                    $(this).remove();
                });
            }, computeStayTime());
        };

        var computeStayTime = function(message) {
            // Avg characters per word: 5.1
            // Avg reading speed of 200 wpm:
            //var totalChara
            return 3000;
        };
    });
})();


(function() {
    var missionsListApp = angular.module('app', []);

    missionsListApp.controller("missionsListController", ['$scope', function($scope) {
        $scope.missions = laravel.missions;

        // Cheap way to get the next launch (only use on future mission page)
        $scope.nextLaunch = function() {
            return $scope.missions[0];
        };

        // Cheap way to get the previous launch (only use on past mission page)
        $scope.lastLaunch = function() {
            return $scope.missions[$scope.missions.length - 1];
        };

        $scope.currentYear = function() {
            return moment().year();
        };

        $scope.missionsInYear = function(year, completeness) {
            return $scope.missions.filter(function(mission) {
                return moment(mission.launch_date_time).year() == year && mission.status == completeness;
            }).length;
        };
    }]);
})();
(function() {
    var missionControlApp = angular.module("app", []);

    missionControlApp.controller("missionControlController", ["$scope", "missionControlService", function($scope, missionControlService) {
        $scope.hasSearchResults = false;
        $scope.isCurrentlySearching = false;
        $scope.pageTitle = "Mission Control";

        $scope.$on('startedSearching', function() {
            $scope.hasSearchResults = false;
            $scope.isCurrentlySearching = true;
            $scope.pageTitle = "Searching...";
        });

        $scope.$on('finishedSearching', function(event, arg) {
            $scope.hasSearchResults = true;
            $scope.isCurrentlySearching = false;
            $scope.pageTitle = '"' + arg + '" results';
        });

        $scope.$on('exitSearchMode', function(event, arg) {
            $scope.hasSearchResults = $scope.isCurrentlySearching = false;
            $scope.pageTitle = "Mission Control";
        });

        $scope.missioncontrol = {
            objects: {
                visibleSection: 'latest',
                show: function(sectionToShow) {
                    $scope.missioncontrol.objects.visibleSection = sectionToShow;
                }
            },
            leaderboards: {
                visibleSection: 'week',
                show: function(sectionToShow) {
                    $scope.missioncontrol.leaderboards.visibleSection = sectionToShow;
                }
            }
        }
    }]);

    missionControlApp.controller("searchController", ["$scope", "$rootScope", "missionControlService", function($scope, $rootScope, missionControlService) {

        $scope.search = function() {

            // Get query and broadcast
            var currentQuery = $scope.currentSearch.toQuery();
            $rootScope.$broadcast('startedSearching');

            // Make request
            missionControlService.search(currentQuery).then(function(response) {
                $rootScope.$broadcast('finishedSearching', currentQuery.searchTerm);
                $scope.searchResults = response.data;
            });
        };
    }]);

    missionControlApp.service("missionControlService", ["$http", function($http) {
        this.search = function(currentQuery) {
            return $http.post('/missioncontrol/search', { search: currentQuery });
        };
    }]);
})();
/**
 * Workaround to make defining and retrieving angular modules easier and more intuitive.
 */
(function (angular) {
    var origMethod = angular.module;

    var alreadyRegistered = {};

    /**
     * Register/fetch a module.
     *
     * @param name {string} module name.
     * @param reqs {array} list of modules this module depends upon.
     * @param configFn {function} config function to run when module loads (only applied for the first call to create this module).
     * @returns {*} the created/existing module.
     */
    angular.module = function (name, reqs, configFn) {
        reqs = reqs || [];
        var module = null;

        if (alreadyRegistered[name]) {
            module = origMethod(name);
            module.requires.push.apply(module.requires, reqs);
        } else {
            module = origMethod(name, reqs, configFn);
            alreadyRegistered[name] = module;
        }

        return module;
    };
})(angular);
(function() {
    var app = angular.module('app', []);

    app.controller("futureMissionController", ['$http', '$scope', '$filter', 'flashMessage', function($http, $scope, $filter, flashMessage) {

        $scope.missionSlug = laravel.mission.slug;
        $scope.launchSpecificity = laravel.mission.launch_specificity;
        $scope.isLaunchPaused = laravel.mission.launch_paused;

        if ($scope.launchSpecificity >= 6) {
            $scope.launchDateTime = moment.utc(laravel.mission.launch_date_time);
        } else {
            $scope.launchDateTime = laravel.mission.launch_date_time;
        }

        $scope.$watch("launchSpecificity", function(newValue) {
            $scope.isLaunchExact = newValue >= 6;
        });

        $scope.lastRequest = moment().utc();
        $scope.secondsSinceLastRequest = $scope.secondsToLaunch = 0;

        $scope.requestFrequencyManager = function() {
            $scope.secondsSinceLastRequest = moment.utc().diff($scope.lastRequest, 'second');
            $scope.secondsToLaunch = moment.utc().diff(moment.utc($scope.launchDateTime, 'YYYY-MM-DD HH:mm:ss'), 'second');

            /*
             Make requests to the server for launchdatetime and webcast updates at the following frequencies:
             >24hrs to launch    =   1hr / request
             1hr-24hrs           =   15min / request
             20min-1hr           =   5 min / request
             <20min              =   30sec / request
             */
            var aRequestNeedsToBeMade = ($scope.secondsToLaunch <= -86400 && $scope.secondsSinceLastRequest >= 3600) ||
                ($scope.secondsToLaunch <= -3600 && $scope.secondsToLaunch > -86400 && $scope.secondsSinceLastRequest >= 900) ||
                ($scope.secondsToLaunch <= -1200 && $scope.secondsToLaunch > -3600 && $scope.secondsSinceLastRequest >= 300) ||
                ($scope.secondsToLaunch > -1200 && $scope.secondsSinceLastRequest >= 30);

            if (aRequestNeedsToBeMade === true) {
                // Make both requests then update the time since last request
                $scope.requestLaunchDateTime();
                $scope.requestWebcastStatus();
                $scope.lastRequest = moment().utc();
            }
        };

        $scope.requestLaunchDateTime = function() {
            $http.get('/missions/' + $scope.missionSlug + '/launchdatetime')
                .then(function(response) {
                    // If there has been a change in the launch datetime, update
                        if (!$scope.launchDateTime.isSame(moment.utc(response.data.launchDateTime))) {

                            $scope.launchDateTime = moment.utc(response.data.launchDateTime);
                            $scope.launchSpecificity = response.data.launchSpecificity;
                            $scope.isLaunchPaused = response.data.launchPaused;
                            flashMessage.addOK('Launch time updated!');

                        }
                });
        };

        $scope.requestWebcastStatus = function() {
            $http.get('/webcast/getstatus')
                .then(function(response) {
                    $scope.webcast.isLive = response.data.isLive;
                    $scope.webcast.viewers = response.data.viewers;
                });
        };

        $scope.webcast = {
            isLive: laravel.webcast.isLive,
            viewers: laravel.webcast.viewers
        };

        $scope.$watchCollection('[webcast.isLive, secondsToLaunch]', function(newValues) {
            if (newValues[1] < (60 * 60 * 24) && newValues[0] == 'true') {
                $scope.webcast.status = 'webcast-live';
            } else if (newValues[1] < (60 * 60 * 24) && newValues[0] == 'false') {
                $scope.webcast.status = 'webcast-updates';
            } else {
                $scope.webcast.status = 'webcast-inactive';
            }
        });

        $scope.$watch('webcast.status', function(newValue) {
            if (newValue === 'webcast-live') {
                $scope.webcast.publicStatus = 'Live Webcast'
            } else if (newValue === 'webcast-updates') {
                $scope.webcast.publicStatus = 'Launch Updates'
            }
        });

        $scope.$watch('webcast.viewers', function(newValue) {
            $scope.webcast.publicViewers = ' (' + newValue + ' viewers)';
        });
    }]);
})();
    (function() {
    var uploadApp = angular.module('app', []);

    uploadApp.controller("uploadAppController", ["$scope", function($scope) {
        $scope.activeSection = "upload";
        $scope.showRecentAdditions = true;

        $scope.data = {
            missions: laravel.missions,
            tags: laravel.tags,
            subtypes: {
                images: [
                    'Mission Patch',
                    'Photo',
                    'Chart',
                    'Concept Art',
                    'Screenshot',
                    'Infographic',
                    'News Summary',
                    'Hazard Map'
                ],
                videos: [
                    'Launch Video',
                    'Press Conference'
                ],
                documents: [
                    'Press Kit',
                    'Cargo Manifest',
                    'Weather Forecast',
                    'License'
                ]
            },
            publishers: laravel.publishers,
            recentUploads: laravel.recentUploads
        };

        $scope.changeSection = function(section) {
            $scope.activeSection = section;
            $scope.showRecentAdditions = section == 'upload';
        };

        $scope.$on('hideSubmissionMethods', function() {
            $scope.areSubmissionMethodsHidden = true;
        });

    }]);

    uploadApp.controller("uploadController", ["$rootScope", "$scope", "objectFromFile", "uploadService", function($rootScope, $scope, objectFromFile, uploadService) {
        $scope.activeUploadSection = "dropzone";
        $scope.isSubmitting = false;
        $scope.isUploading = false;
        $scope.queuedFiles = 0;

        $scope.currentVisibleFile = null;
        $scope.isVisibleFile = function(file) {
            return $scope.currentVisibleFile === file;
        };
        $scope.setVisibleFile = function(file) {
            $scope.currentVisibleFile = file;
        };

        $scope.uploadCallback = function() {
            $scope.isUploading = false;

            // Once files have been successfully upload, convert to Objects
            $scope.files.forEach(function(file, index) {
                file = objectFromFile.create(file, index);

                // Set the initial visible file
                if (index === 0) {
                    $scope.currentVisibleFile = file;
                }
            });

            // Change the upload section
            $scope.activeUploadSection = "data";
            $rootScope.$broadcast('hideSubmissionMethods');
            $scope.showRecentAdditions = false;
            $scope.$apply();
        };

        $scope.optionalCollection = null;

        $scope.fileSubmitButtonText = function(form) {
            if (form.$invalid) {
                return 'We need more info';
            } else if ($scope.isSubmitting) {
                return 'Submitting...';
            } else {
                return 'Submit';
            }
        };

        $scope.fileSubmitButtonFunction = function() {
            $scope.isSubmitting = true;
            uploadService.postToMissionControl($scope.files, 'files', $scope.optionalCollection);
        }
    }]);

    uploadApp.controller("postController", ["$scope", "uploadService", function($scope, uploadService) {

        $scope.NSFcomment = {};
        $scope.redditcomment = {};
        $scope.pressrelease = {};
        $scope.article = {};
        $scope.tweet = {};

        $scope.isSubmitting = false;

        $scope.detectPublisher = function() {

        };

        $scope.postSubmitButtonText = function(form) {
            if (form.$invalid) {
                return 'We need more info';
            } else if ($scope.isSubmitting) {
                return 'Submitting...';
            } else {
                return 'Submit';
            }
        };

        $scope.postSubmitButtonFunction = function() {
            $scope.isSubmitting = true;
            switch ($scope.postType) {
                case 'NSFcomment': uploadService.postToMissionControl($scope.NSFcomment, 'NSFcomment'); break;
                case 'redditcomment': uploadService.postToMissionControl($scope.redditcomment, 'redditcomment'); break;
                case 'pressrelease' : uploadService.postToMissionControl($scope.pressrelease, 'pressrelease'); break;
                case 'article': uploadService.postToMissionControl($scope.article, 'article'); break;
                case 'tweet': uploadService.postToMissionControl($scope.tweet, 'tweet'); break;
            }
        }
    }]);

    uploadApp.controller("writeController", ["$scope", "uploadService", function($scope, uploadService) {

        $scope.text = {
            title: null,
            summary: null,
            mission_id: null,
            anonymous: null,
            tags: []
        };

        $scope.isSubmitting = false;

        $scope.writeSubmitButtonText = function(form) {
            if (form.$invalid) {
                return 'We need more info';
            } else if ($scope.isSubmitting) {
                return 'Submitting...';
            } else {
                return 'Submit';
            }
        };

        $scope.writeSubmitButtonFunction = function() {
            $scope.isSubmitting = true;
            uploadService.postToMissionControl($scope.text, 'text');
        }
    }]);

    uploadApp.service('uploadService', ['$http', 'CSRF_TOKEN', 'flashMessage', function($http, CSRF_TOKEN, flashMessage) {
        this.postToMissionControl = function(dataToUpload, resourceType, collection) {
            var submissionData = {
                data: dataToUpload,
                collection: collection,
                type: resourceType,
                _token: CSRF_TOKEN
            };

            if (resourceType == 'files') {
                submitFiles(submissionData).then(redirect, error);
            } else if (["article", "pressrelease", "tweet", "redditcomment", "NSFcomment"].indexOf(resourceType) !== -1) {
                submitPost(submissionData).then(redirect, error);
            } else if (resourceType == "text") {
                submitWriting(submissionData).then(redirect, error);
            }
        };

        var submitFiles = function(submissionData) {
            return $http.put('/missioncontrol/create/submit/files', submissionData);
        };

        var submitPost = function(submissionData) {
            return $http.put('/missioncontrol/create/submit/post', submissionData);
        };

        var submitWriting = function(submissionData) {
            return $http.put('/missioncontrol/create/submit/writing', submissionData);
        };

        var redirect = function(response) {
            window.location = '/missioncontrol';
        };

        var error = function(response) {
            flashMessage.addError(response.data.errors);
        };
    }]);

    uploadApp.factory("Image", function() {
        return function (image, index) {
            var self = image;

            self.index = index;

            self.title = null;
            self.summary = null;
            self.subtype = null;
            self.mission_id = null;
            self.author = null;
            self.attribution = null;
            self.anonymous = null;
            self.tags = [];

            self.datetimeExtractedFromEXIF = angular.isDefined(self.originated_at) ? true : false;

            return self;
        }
    });

    uploadApp.factory("GIF", function() {
        return function(gif, index) {
            var self = gif;

            self.index = index;

            self.title = null;
            self.summary = null;
            self.subtype = null;
            self.mission_id = null;
            self.author = null;
            self.attribution = null;
            self.anonymous = null;
            self.tags = [];
            self.originated_at = null;

            return self;
        }
    });

    uploadApp.factory("Audio", function() {
        return function(audio, index) {
            var self = audio;

            self.index = index;

            self.title = null;
            self.summary = null;
            self.subtype = null;
            self.mission_id = null;
            self.author = null;
            self.attribution = null;
            self.anonymous = null;
            self.tags = [];
            self.originated_at = null;

            return self;
        }
    });

    uploadApp.factory("Video", function() {
        return function(video, index) {
            var self = video;

            self.index = index;

            self.title = null;
            self.summary = null;
            self.external_url = null;
            self.subtype = null;
            self.mission_id = null;
            self.author = null;
            self.attribution = null;
            self.anonymous = null;
            self.tags = [];
            self.originated_at = null;

            return self;
        }
    });

    uploadApp.factory("Document", function() {
        return function(document, index) {
            var self = document;

            self.index = index;

            self.title = null;
            self.summary = null;
            self.subtype = null;
            self.mission_id = null;
            self.author = null;
            self.attribution = null;
            self.anonymous = null;
            self.tags = [];
            self.originated_at = null;

            return self;
        }
    });

    uploadApp.service("objectFromFile", ["Image", "GIF", "Audio", "Video", "Document", function(Image, GIF, Audio, Video, Document) {
        this.create = function(file, index) {
            switch(file.type) {
                case 'Image': return new Image(file, index);
                case 'GIF': return new GIF(file, index);
                case 'Audio': return new Audio(file, index);
                case 'Video': return new Video(file, index);
                case 'Document': return new Document(file, index);
                default: return null;
            }
        }
    }]);
})();
(function() {
    var questionsApp = angular.module('app', []);

    questionsApp.controller("questionsController", ["$scope", "questionService", function($scope, questionService) {

        $scope.clearPinnedQuestion = function() {
            history.replaceState('', document.title, window.location.pathname);
            $scope.pinnedQuestion = null;
        };

        $scope.pinQuestion = function(question) {
            history.replaceState('', document.title, '#' + question.slug);
            $scope.pinnedQuestion = question;
        };

        (function() {
            questionService.get().then(function(questions) {
                $scope.questions = questions;

                // Set the pinned question if one is present
                if (window.location.hash) {
                    $scope.pinnedQuestion = $scope.questions.filter(function(q) {
                        return window.location.hash.substring(1) == q.slug;
                    })[0];
                } else {
                    $scope.pinnedQuestion = null;
                }

            });
        })();
    }]);

    questionsApp.service("questionService", ["$http", "Question", function($http, Question) {
        this.get = function() {
            return $http.get('/faq/get').then(function(response) {
                return response.data.map(function(question) {
                    return new Question(question);
                });
            });
        };
    }]);

    questionsApp.factory("Question", function() {
        return function(question) {
            var self = question;

            self.slug = question.question.toLowerCase()
                .replace(/[^\w ]+/g,'')
                .replace(/ +/g,'-');

            return self;
        };
    });

})();
(function() {
	var publisherApp = angular.module('app', []);

	publisherApp.controller('publishersController', ["$scope", "publisherService", "flashMessage", function($scope, publisherService, flashMessage) {
        $scope.publishers = laravel.publishers;
        $scope.isCreatingPublisher = $scope.isEditingPublisher = false;

        $scope.editPublisher = function(publisher) {
            $scope.isEditingPublisher = true;
            publisherService.edit(publisher).then(function(response) {
                publisher = response.data;
                $scope.isEditingPublisher = false;
                flashMessage.addOK("Publisher " + publisher.name + " edited");
            });
        };

        $scope.createPublisher = function(publisher, form) {
            $scope.isCreatingPublisher = true;
            publisherService.create(publisher).then(function(response) {
                $scope.publishers.unshift(response.data);
                $scope.isCreatingPublisher = false;

                flashMessage.addOK("Publisher " + publisher.name + " created");
                $scope.newPublisher = null;
                form.$setUntouched();

            }, function(response) {
                $scope.isCreatingPublisher = false;
                flashMessage.addError("That publisher already exists");
            });
        };

        $scope.deletePublisher = function(publisher) {
            $scope.isDeletingPublisher = true;
            publisherService.delete(publisher).then(function(response) {
                $scope.isDeletingPublisher = false;
                flashMessage.addOK("Publisher " + publisher.name + " deleted");
            });
        }
	}]);

    publisherApp.service('publisherService', ["$http", function($http) {
        this.create = function(publisher) {
            return $http.post('/missioncontrol/publishers/create', {publisher: publisher});
        };

        this.edit = function(publisher) {
            return $http.patch('/missioncontrol/publishers/' + publisher.publisher_id, {publisher: publisher});
        };

        this.delete = function(publisher) {
            return $http.delete('/missioncontrol/publishers/' + publisher.publisher_id);
        };
    }]);
})();
(function() {
    var reviewApp = angular.module('app', []);

    reviewApp.controller("reviewController", ["$scope", 'reviewService', function($scope, reviewService) {
        $scope.isLoading = true;
        $scope.objectsToReview = [];

        $scope.visibilities = ['Default', 'Public', 'Hidden'];

        $scope.action = function(object, status) {

            object.status = status;

            if (status == 'Published') {
                object.isBeingPublished = true;
            } else if (status == 'Deleted') {
                object.isBeingDeleted = true;
            }

            reviewService.review(object).then(function() {
                $scope.objectsToReview.splice($scope.objectsToReview.indexOf(object), 1);

            }, function(response) {
                alert('An error occurred');
                console.log(response);
            })
        };

        $scope.reviewPageSubheading = function() {
            if ($scope.isLoading) {
                return 'Loading Queued Objects...';
            } else {
                if (angular.isDefined($scope.objectsToReview))
                    return '<span>' + $scope.objectsToReview.length + '</span> objects to review';
            }
        };

        (function() {
            reviewService.fetch().then(function(response) {
                console.log(response);
                $scope.objectsToReview = response;
                $scope.isLoading = false;
            });
        })();
    }]);

    reviewApp.service('reviewService', ["$http", "ObjectToReview", function($http, ObjectToReview) {
        this.fetch = function() {
            return $http.get('/missioncontrol/review/get').then(function(response) {

                return response.data.map(function(objectToReview) {
                    return new ObjectToReview(objectToReview);
                });
            });
        };

        this.review = function(object) {
            return $http.post('/missioncontrol/review/update/' + object.object_id, {
                visibility: object.visibility, status: object.status
            });
        };
    }]);

    reviewApp.factory("ObjectToReview", function() {
        return function (object) {
            var self = object;

            self.visibility = "Default";

            self.linkToObject = '/missioncontrol/object/' + self.object_id;
            self.linkToUser = '/users/' + self.user.username;

            self.createdAtRelative = moment.utc(self.created_at).fromNow();

            self.size = self.size / 1000 + ' KB';

            self.isBeingPublished = false;
            self.isBeingDeleted = false;

            return self;
        }
    });
})();
(function() {
    var objectApp = angular.module('app', ['ui.tree', 'ngSanitize'], function($rootScopeProvider) {
        $rootScopeProvider.digestTtl(20);
    });

    objectApp.controller("objectController", ["$scope", "$http", function($scope, $http) {

        $scope.note = laravel.userNote !== null ? laravel.userNote.note : "";
        $scope.object = laravel.object;

        $scope.$watch("note", function(noteValue) {
            if (noteValue === "" || noteValue === null) {
                $scope.noteButtonText = "Create Note";
                $scope.noteReadText = '<p class="exclaim">Create a note!</p>';
            } else {
                $scope.noteButtonText = "Edit Note";
                $scope.noteReadText = noteValue;
            }
        });

        $scope.noteState = "read";
        $scope.changeNoteState = function() {

            $scope.originalNote = $scope.note;

            if ($scope.noteState == "read") {
                $scope.noteState = "write";
            } else {
                $scope.noteState = "read";
            }
        };

        $scope.saveNote = function() {
            if ($scope.originalNote === "") {

                $http.post('/missioncontrol/objects/' + $scope.object.object_id + '/note', {
                    note: $scope.note
                }).then(function() {
                    $scope.changeNoteState();
                });

            } else {

                $http.patch('/missioncontrol/objects/' + $scope.object.object_id + '/note', {
                    note: $scope.note
                }).then(function() {
                    $scope.changeNoteState();
                });
            }
        };

        $scope.deleteNote = function() {
            $http.delete('/missioncontrol/objects/' + $scope.object.object_id + '/note')
                .then(function() {
                    $scope.note = "";
                    $scope.changeNoteState();
                });
        };

        /* FAVORITES */
        $scope.favorites = laravel.totalFavorites;

        $scope.$watch("favorites", function(newFavoritesValue) {
            if (newFavoritesValue == 1) {
                $scope.favoritesText = "1 Favorite";
            }  else {
                $scope.favoritesText = $scope.favorites + " Favorites";
            }
        });

        $scope.isFavorited = laravel.isFavorited !== null;
        $scope.toggleFavorite = function() {

            $scope.isFavorited = !$scope.isFavorited;

            if ($scope.isFavorited === true) {

                var requestType = 'POST';
                $scope.favorites++;
                $http.post('/missioncontrol/objects/' + $scope.object.object_id + '/favorite');

            } else if ($scope.isFavorited === false) {

                var requestType = 'DELETE';
                $scope.favorites--;
                $http.delete('/missioncontrol/objects/' + $scope.object.object_id + '/favorite');

            }
        };

        /* DOWNLOAD */
        $scope.incrementDownloads = function() {
            $http.get('/missioncontrol/objects/' + $scope.object.object_id + '/download');
        }

    }]);

    objectApp.controller('commentsController', ["$scope", "commentService", "Comment", "flashMessage", function($scope, commentService, Comment, flashMessage) {
        $scope.object = laravel.object;
        $scope.commentsAreLoaded = false;
        $scope.isAddingTopLevelComment = false;

        $scope.addTopLevelComment = function(form) {
            $scope.isAddingTopLevelComment = true;
            commentService.addTopLevel($scope.object, $scope.newComment).then(function(response) {
                $scope.isAddingTopLevelComment = false;
                $scope.comments.push(new Comment(response.data));
                $scope.newComment = null;
                form.$setUntouched();
                    flashMessage.addOK('Comment submitted');
            },
            function(response) {
                $scope.isAddingTopLevelComment = false;
                flashMessage.addError('Comment could not be submitted. Try again or contact us.');
            });
        };

        (function() {
            commentService.get($scope.object).then(function(response) {
                $scope.comments = response.data.map(function(comment) {
                    return new Comment(comment);
                });
                $scope.commentsAreLoaded = true;
            });
        })();

    }]);

    objectApp.service("noteService", ["$http", function($http) {

    }]);

    objectApp.service("favoriteService", ["$http", function($http) {

    }]);

    objectApp.service("commentService", ["$http",
        function($http) {

            this.get = function (object) {
                return $http.get('/missioncontrol/objects/' + object.object_id + '/comments');
            };

            this.addTopLevel = function(object, comment) {
                return $http.post('/missioncontrol/objects/' + object.object_id + '/comments/create', { comment: {
                    comment: comment,
                    parent: null
                }});
            };

            this.addReply = function(object, reply, parent) {
                return $http.post('/missioncontrol/objects/' + object.object_id + '/comments/create', { comment: {
                    comment: reply,
                    parent: parent.comment_id
                }});
            };

            this.delete = function(object, comment) {
                return $http.delete('/missioncontrol/objects/' + object.object_id + '/comments/' + comment.comment_id);
            };

            this.edit = function(object, comment) {
                return $http.patch('/missioncontrol/objects/' + object.object_id + '/comments/' + comment.comment_id, { comment: {
                    comment: comment.editText
                }});
            };
        }
    ]);

    objectApp.factory("Comment", ["commentService", function(commentService) {
        function Comment(comment) {
            var self = comment;

            if (typeof self.children === 'undefined') {
                self.children = [];
            }

            self.isReplying = false;
            self.isEditing = false;
            self.isDeleting = false;

            self.isSending = {
                reply: false,
                edit: false,
                deletion: false
            };

            self.toggleReplyState = function() {
                if (!self.isReplying) {
                    self.isReplying = true;
                    self.isEditing = self.isDeleting = false;
                } else {
                    self.isReplying = false;
                }
            };

            self.toggleEditState = function() {
                if (!self.isEditing) {
                    self.isEditing = true;
                    self.isReplying = self.isDeleting = false;
                } else {
                    self.isEditing = false;
                }
            };

            self.toggleDeleteState = function() {
                if (!self.isDeleting) {
                    self.isDeleting = true;
                    self.isReplying = self.isEditing = false;
                } else {
                    self.isDeleting = false;
                }
            };

            self.editText = self.comment;

            self.reply = function() {
                self.isSending.reply = true;
                commentService.addReply(laravel.object, self.replyText, self).then(function(response) {
                    self.replyText = null;
                    self.isReplying = self.isSending.reply = false;

                    self.children.push(new Comment(response.data));
                });
            };

            self.edit = function() {
                self.isSending.edit = true;
                commentService.edit(laravel.object, self).then(function(response) {
                    self.comment_md = response.data.comment_md;
                    self.comment = self.editText;
                    self.editText = null;
                    self.isEditing = self.isSending.edit = false;
                });
            };

            self.delete = function(scope) {
                self.isSending.deletion = true;
                commentService.delete(laravel.object, self).then(function() {
                    self.comment = self.comment_md = null;
                    self.isDeleting = self.isSending.deletion = false;

                    // If the comment has no children, remove it entirely. Otherwise, just show [deleted], similar to Reddit
                    if (self.children.length === 0) {
                        scope.$parent.remove();
                    } else {
                        self.isHidden = true;
                    }
                });
            };

            self.children = self.children.map(function(reply) {
                return new Comment(reply);
            });

            return self;
        }

        return Comment;
    }]);
})();
(function() {
    var app = angular.module('app', []);

    app.controller("missionController", ['$scope', 'missionService', function($scope, missionService) {
        // Scope the possible form data info
        $scope.data = {
            parts: laravel.parts,
            spacecraft: laravel.spacecraft,
            destinations: laravel.destinations,
            missionTypes: laravel.missionTypes,
            launchSites: laravel.launchSites,
            landingSites: laravel.landingSites,
            vehicles: laravel.vehicles,
            astronauts: laravel.astronauts,

            launchVideos: laravel.launchVideos ? laravel.launchVideos : null,
            missionPatches: laravel.missionPatches ? laravel.missionPatches : null,
            pressKits: laravel.pressKits ? laravel.pressKits : null,
            cargoManifests: laravel.cargoManifests ? laravel.cargoManifests : null,
            pressConferences: laravel.pressConferences ? laravel.pressConferences : null,
            featuredImages: laravel.featuredImages ? laravel.featuredImages: null,

            firstStageEngines: ['Merlin 1A', 'Merlin 1B', 'Merlin 1C', 'Merlin 1D'],
            upperStageEngines: ['Kestrel', 'Merlin 1C-Vac', 'Merlin 1D-Vac'],
            upperStageStatuses: ['Did not reach orbit', 'Decayed', 'Deorbited', 'Earth Orbit', 'Solar Orbit'],
            spacecraftTypes: ['Dragon 1', 'Dragon 2'],
            returnMethods: ['Splashdown', 'Landing', 'Did Not Return'],
            eventTypes: ['Wet Dress Rehearsal', 'Static Fire'],
            launchIlluminations: ['Day', 'Night', 'Twilight'],
            statuses: ['Upcoming', 'Complete', 'In Progress'],
            outcomes: ['Failure', 'Success'],
			specificities: [
				{'value' : 0, 'text' : 'Year'},
				{'value' : 1, 'text' : 'Half'},
				{'value' : 2, 'text' : 'Subyear'},
				{'value' : 3, 'text' : 'Quarter'},
				{'value' : 4, 'text' : 'Month'},
				{'value' : 5, 'text' : 'Submonth'},
				{'value' : 6, 'text' : 'Day'},
				{'value' : 7, 'text' : 'Precise'}	
			]
        };

        $scope.filters = {
            parts: {
                type: ''
            }
        };

        $scope.mission = {
            data: laravel.mission,
            make: function() {
                missionService.make($scope.mission.data);
            }
        };
    }]);

    app.service("missionService", ["$http", "CSRF_TOKEN", function($http, CSRF_TOKEN) {
        this.make = function(mission) {
            if (mission.mission_id == null) {
                return $http.post('/missions/create', {
                    mission: mission,
                    _token: CSRF_TOKEN
                }).then(function (response) {
                    window.location = '/missions/' + response.data;
                });
            } else {
                return $http.patch('/missions/' + mission.slug + '/edit', {
                    mission: mission,
                    _token: CSRF_TOKEN
                }).then(function(response){
					console.log(response);
				});;
            }
        };
    }]);
})();
(function() {
    var aboutMissionControlApp = angular.module('app', ['credit-cards']);

    aboutMissionControlApp.controller("subscriptionController", ["$scope", "subscriptionService", function($scope, subscriptionService) {
        $scope.subscriptionButtonText = "Pay $9";
        $scope.subscriptionState = {
            isLooking: true,
            isEnteringDetails: false,
            isSubscribing: false,
            hasSubscribed: false,
            failed: false
        };

        $scope.subscription = {
            showSubscribeForm: function() {
                $scope.subscriptionState.isLooking = false;
                $scope.subscriptionState.isEnteringDetails = true;
            },
            subscribe: function($event) {
                $scope.subscriptionState.isEnteringDetails =  $scope.subscriptionState.failed = false;
                $scope.subscriptionState.isSubscribing = true;
                $scope.subscriptionButtonText = "You're awesome";

                var form = $('form[name="subscribeForm"]');
                Stripe.card.createToken(form, $scope.subscription.stripeResponseHandler);
            },
            stripeResponseHandler: function(stripeStatus, stripeResponse) {

                if (stripeResponse.error) {
                    $scope.subscriptionState.isSubscribing = false;
                    $scope.subscriptionState.isEnteringDetails = $scope.subscriptionState.failed = true;
                } else {
                    // Fetch the token from Stripe's response.
                    var token = stripeResponse.id;

                    // Subscribe
                    subscriptionService.subscribe(token).then(function() {
                        // Success!
                        $scope.subscriptionState.isSubscribing = false;
                        $scope.subscriptionState.hasSubscribed = true;
                    });
                }

                $scope.$apply();
            }
        };
    }]);

    aboutMissionControlApp.controller('aboutController', ["$scope", function($scope) {

    }]);

    aboutMissionControlApp.service("subscriptionService", ["$http", function($http) {
        this.subscribe = function(token) {
            return $http.post('/missioncontrol/payments/subscribe', { creditCardToken: token });
        };
    }]);

    aboutMissionControlApp.service("aboutMissionControlService", ["$http", function($http) {
    }]);
})();

(function() {
    var dataViewApp = angular.module('app', []);

    dataViewApp.controller('dataViewController', ['DataView', 'dataViewService', '$scope', '$http', function(DataView, dataViewService, $scope, $http) {
        $scope.newDataView = new DataView();
        $scope.dataViews = [];

        $scope.create = function(dataViewToCreate) {
            dataViewService.create(dataViewToCreate).then(function(response) {
                $scope.newDataView = new DataView();
            });
        };

        $scope.edit = function(dataViewToEdit) {
            dataViewService.edit(dataViewToEdit).then(function(response) {

            });
        };

        (function() {
            $scope.data = {
                bannerImages: laravel.bannerImages
            };

            laravel.dataViews.forEach(function(dataView) {
                $scope.dataViews.push(new DataView(dataView));
            });
        })();
    }]);

    dataViewApp.service('dataViewService', ["$http", function($http) {
        this.testQuery = function(query) {
            var encodedQuery = encodeURIComponent(query);
            return $http.get('/missioncontrol/dataviews/testquery?q=' + encodedQuery);
        };

        this.create = function(data) {
            return $http.post('/missioncontrol/dataviews/create',{ dataView: data });
        };

        this.edit = function(data) {
            return $http.post('/missioncontrol/dataviews/' + data.dataview_id + '/edit', { dataView: data });
        };
    }]);

    dataViewApp.factory('DataView', ['dataViewService', function(dataViewService) {
        return function(dataView) {

            if (typeof dataView === 'undefined') {
                var self = this
            } else {
                var self = dataView;
            }

            if (typeof dataView === 'undefined') {
                self.column_titles = [];
            }

            self.addTitle = function(newTitle) {
                if (typeof newTitle !== 'undefined' && newTitle != "") {
                    self.column_titles.push(newTitle);
                    self.newTitle = undefined;
                }
            };

            self.deleteTitle = function() {

            };

            self.testQuery = function() {
                dataViewService.testQuery(self.query).then(function(response) {
                    self.testQueryOutput = response.data;
                });
            }
        }
    }]);

})();
(function() {
    var app = angular.module('app', []);

    app.controller('pastMissionController', ["$scope", "missionDataService", "telemetryPlotCreator", "ephemerisPlotCreator", function($scope, missionDataService, telemetryPlotCreator, ephemerisPlotCreator) {
        $scope.mission = laravel.mission;

        (function() {
            missionDataService.telemetry($scope.mission.slug).then(function(response) {
                $scope.telemetryPlots = {
                    altitudeVsTime:         telemetryPlotCreator.altitudeVsTime(response.data),
                    altitudeVsDownrange:    telemetryPlotCreator.altitudeVsDownrange(response.data),
                    velocityVsTime:         telemetryPlotCreator.velocityVsTime(response.data),
                    downrangeVsTime:        telemetryPlotCreator.downrangeVsTime(response.data)
                }
            });
            missionDataService.orbitalElements($scope.mission.slug).then(function(response) {
                $scope.orbitalPlots = {
                    apogeeVsTime:           ephemerisPlotCreator.apogeeVsTime(response.data),
                    perigeeVsTime:          ephemerisPlotCreator.perigeeVsTime(response.data),
                    inclinationVsTime:      ephemerisPlotCreator.inclinationVsTime(response.data)
                }
            });
        })();
    }]);

    app.service('telemetryPlotCreator', [function() {
        this.altitudeVsTime = function(telemetryCollection) {
            return {
                data: telemetryCollection.filter(function(telemetry) {
                    return telemetry.altitude != null;
                }).map(function(telemetry) {
                    return { timestamp: telemetry.timestamp, altitude: telemetry.altitude };
                }),
                settings: {
                    extrapolation: true,
                    interpolation: 'cardinal',
                    xAxis: {
                        type: 'linear',
                        key: 'timestamp',
                        title: 'Time (T+s)'
                    },
                    yAxis: {
                        type: 'linear',
                        key: 'altitude',
                        title: 'Altitude (km)',
                        formatter: function(d) {
                            return d / 1000;
                        }
                    },
                    chartTitle: 'Altitude vs. Time'
                }
            }
        };

        this.altitudeVsDownrange = function(telemetryCollection) {
            return {
                data: telemetryCollection.filter(function(telemetry) {
                    return (telemetry.downrange != null && telemetry.altitude != null);
                }).map(function(telemetry) {
                    return { downrange: telemetry.downrange, altitude: telemetry.altitude };
                }),
                settings: {
                    extrapolation: true,
                    interpolation: 'cardinal',
                    xAxis: {
                        type: 'linear',
                        key: 'downrange',
                        title: 'Downrange Distance (km)',
                        formatter: function(d) {
                            return d / 1000;
                        }
                    },
                    yAxis: {
                        type: 'linear',
                        key: 'altitude',
                        title: 'Altitude (km)',
                        formatter: function(d) {
                            return d / 1000;
                        }
                    },
                    chartTitle: 'Altitude vs. Downrange Distance'
                }
            }
        };

        this.velocityVsTime = function(telemetryCollection) {
            return {
                data: telemetryCollection.filter(function(telemetry) {
                    return telemetry.velocity != null;
                }).map(function(telemetry) {
                    return { timestamp: telemetry.timestamp, velocity: telemetry.velocity };
                }),
                settings: {
                    extrapolation: true,
                    interpolation: 'cardinal',
                    xAxis: {
                        type: 'linear',
                        key: 'timestamp',
                        title: 'Time (T+s)'
                    },
                    yAxis: {
                        type: 'linear',
                        key: 'velocity',
                        title: 'Velocity (m/s)'
                    },
                    chartTitle: 'Velocity vs. Time'
                }
            }
        };

        this.downrangeVsTime = function(telemetryCollection) {
            return {
                data: telemetryCollection.filter(function(telemetry) {
                    return telemetry.downrange != null;
                }).map(function(telemetry) {
                    return { timestamp: telemetry.timestamp, downrange: telemetry.downrange };
                }),
                settings: {
                    extrapolation: true,
                    interpolation: 'cardinal',
                    xAxis: {
                        type: 'linear',
                        key: 'timestamp',
                        title: 'Time (T+s)'
                    },
                    yAxis: {
                        type: 'linear',
                        key: 'downrange',
                        title: 'Downrange Distance (km)',
                        formatter: function(d) {
                            return d / 1000;
                        }
                    },
                    chartTitle: 'Downrange Distance vs. Time'
                }
            }
        };
    }]);

    app.service('ephemerisPlotCreator', [function() {
        this.apogeeVsTime = function(orbitalElements) {
            return {
                data: orbitalElements.map(function(orbitalElement) {
                        return {
                            timestamp: orbitalElement.epoch,
                            apogee: orbitalElement.apogee
                        };
                }),
                settings: {
                    extrapolation: false,
                    interpolation: 'linear',
                    xAxis: {
                        type: 'timescale',
                        key: 'timestamp',
                        title: 'Epoch Date',
                        ticks: 10,
                        formatter: function(d) {
                            return moment(d).format('MMM D, YYYY');
                        }
                    },
                    yAxis: {
                        type: 'linear',
                        key: 'apogee',
                        title: 'Apogee (km)',
                        formatter: function(d) {
                            return Math.round(d * 10) / 10; // Round to 1dp
                        }
                    },
                    chartTitle: 'Apogee (km) vs. Time'
                }
            }
        };

        this.perigeeVsTime = function(orbitalElements) {
            return {
                data: orbitalElements.map(function(orbitalElement) {
                    return {
                        timestamp: orbitalElement.epoch,
                        perigee: orbitalElement.perigee
                    };
                }),
                settings: {
                    extrapolation: false,
                    interpolation: 'linear',
                    xAxis: {
                        type: 'timescale',
                        key: 'timestamp',
                        title: 'Epoch Date',
                        ticks: 10,
                        formatter: function(d) {
                            return moment(d).format('MMM D, YYYY');
                        }
                    },
                    yAxis: {
                        type: 'linear',
                        key: 'perigee',
                        title: 'Perigee (km)',
                        formatter: function(d) {
                            return Math.round(d * 10) / 10; // Round to 1dp
                        }
                    },
                    chartTitle: 'Perigee (km) vs. Time'
                }
            }
        };

        this.inclinationVsTime = function(orbitalElements) {
            return {
                data: orbitalElements.map(function(orbitalElement) {
                    return {
                        timestamp: orbitalElement.epoch,
                        inclination: orbitalElement.perigee
                    };
                }),
                settings: {
                    extrapolation: false,
                    interpolation: 'linear',
                    xAxis: {
                        type: 'timescale',
                        key: 'timestamp',
                        title: 'Epoch Date',
                        ticks: 10,
                        formatter: function(d) {
                            return moment(d).format('MMM D, YYYY');
                        }
                    },
                    yAxis: {
                        type: 'linear',
                        key: 'inclination',
                        title: 'Inclination (km)',
                        formatter: function(d) {
                            return Math.round(d * 10) / 10 + '°'; // Round to 1dp
                        }
                    },
                    chartTitle: 'Inclination (°) vs. Time'
                }
            }
        };
    }]);
})();
(function() {
    var editObjectApp = angular.module('app', []);

    editObjectApp.controller("editObjectController", ["$scope", "editObjectService", function($scope, editObjectService) {

        $scope.edit = function() {
            editObjectService.edit($scope.object, $scope.metadata);
        };

        $scope.revert = function() {
            editObjectService.revert($scope.object, $scope.selectedRevision);
        };

        (function() {
            $scope.object = laravel.object;
            $scope.revisions = laravel.revisions;
        })();
    }]);

    editObjectApp.service("editObjectService", ["$http", function($http) {
        this.edit = function(object, metadata) {
            return $http.patch('/missioncontrol/objects/' + object.object_id + '/edit', {
                metadata: metadata,
                object: object
            }).then(function(response) {
                window.location.href = '/missioncontrol/objects/' + object.object_id;
            });
        };

        this.revert = function(object, revertTo) {
            return $http.patch('/missioncontrol/objects/' + object.object_id + '/revert/' + revertTo.object_revision_id).then(function(response) {
                window.location.href = '/missioncontrol/objects/' + object.object_id;
            });
        };

        this.addToCollection = function(object, collection) {

        };
    }]);
})();
(function() {
    var app = angular.module('app', ['duScroll', 'ngAnimate']);

    app.controller("homeController", ['$scope', '$rootScope', '$document', '$window', 'Statistic', function($scope, $rootScope, $document, $window, Statistic) {
        $scope.statistics = [];
        $scope.activeStatistic = null;

        $scope.goToClickedStatistic = function(statistic) {
            $scope.scrollToAndMakeActive(statistic);
        };

        $scope.goToFirstStatistic = function() {
            $scope.scrollToAndMakeActive($scope.statistics[0]);
        };

        $scope.goToNeighborStatistic = function(index) {
            if (index >= 0 && index < $scope.statistics.length) {
                $scope.scrollToAndMakeActive($scope.statistics[index]);
                return $scope.activeStatistic.camelCaseType;

            } else {
                $scope.goHome();
            }
        };

        $scope.goHome = function() {
            $scope.scrollToAndMakeActive(null, true);
        };

        $scope.keypress = function(event) {
            // Currently using jQuery.event.which to detect keypresses, keyCode is deprecated, use KeyboardEvent.key eventually:
            // https://developer.mozilla.org/en-US/docs/Web/API/KeyboardEvent/key

            // event.key == down
            if (event.which == 40) {
                if ($scope.activeStatistic == null) {
                    $scope.goToFirstStatistic();

                } else if ($scope.activeStatistic == $scope.statistics[$scope.statistics.length - 1]) {
                    $scope.goHome();

                } else {
                    $scope.scrollToAndMakeActive($scope.statistics[$scope.statistics.indexOf($scope.activeStatistic) + 1]);
                }
            }

            // event.key == up
            else if (event.which == 38) {
                if ($scope.activeStatistic == null) {
                    $scope.scrollToAndMakeActive($scope.statistics[$scope.statistics.length - 1]);

                } else if ($scope.activeStatistic == $scope.statistics[0]) {
                    $scope.goHome();

                } else {
                    $scope.scrollToAndMakeActive($scope.statistics[$scope.statistics.indexOf($scope.activeStatistic) - 1]);
                }
            }

            // event.key == left
            else if (event.which == 37) {
                if ($scope.activeStatistic == null) {
                    return;
                }

                if ($scope.activeStatistic.activeSubstatistic == $scope.activeStatistic.substatistics[0]) {
                    $scope.activeStatistic.changeSubstatistic($scope.activeStatistic.substatistics[$scope.activeStatistic.substatistics.length - 1]);

                } else {
                    $scope.activeStatistic.changeSubstatistic($scope.activeStatistic.substatistics[$scope.activeStatistic.substatistics.indexOf($scope.activeStatistic.activeSubstatistic) - 1]);
                }
            }

            else if (event.which == 39) {
                if ($scope.activeStatistic == null) {
                    return;
                }

                if ($scope.activeStatistic.activeSubstatistic == $scope.activeStatistic.substatistics[$scope.activeStatistic.substatistics.length - 1]) {
                    $scope.activeStatistic.changeSubstatistic($scope.activeStatistic.substatistics[0]);

                } else {
                    $scope.activeStatistic.changeSubstatistic($scope.activeStatistic.substatistics[$scope.activeStatistic.substatistics.indexOf($scope.activeStatistic.activeSubstatistic) + 1]);
                }
            }

        };

        $scope.scrollToAndMakeActive = function(statistic, setToDefault) {
            if (setToDefault === true) {
                history.replaceState('', document.title, window.location.pathname);
                $scope.activeStatistic = null;
                $document.scrollToElement(angular.element(document.getElementById('home')), 0, 1000);;
            } else {
                $scope.activeStatistic = statistic;
                $document.scrollToElement(angular.element(document.getElementById(statistic.camelCaseType)), 0, 1000);
            }
            return $scope.activeStatistic;
        };

        $rootScope.$on('duScrollspy:becameActive', function($event, $element, $target) {
            if ($element.prop('id') == 'home') {
                history.replaceState('', document.title, window.location.pathname);
                $scope.activeStatistic = null;
            } else {
                $scope.activeStatistic = $scope.statistics.filter(function(statistic) {
                    return statistic.camelCaseType == $element.prop('id');
                })[0];
                history.replaceState('', document.title, '#' + $scope.activeStatistic.camelCaseType);
            }
            $scope.$apply();
        });

        (function() {
            laravel.statistics.forEach(function(statistic) {
                $scope.statistics.push(new Statistic(statistic));
            });

            // If a hash exists, preset it
            if (window.location.hash) {
                $scope.activeStatistic = $scope.statistics.filter(function(statistic) {
                    return statistic.camelCaseType == window.location.hash.substring(1);
                })[0];
            }
        })();
    }]);

    app.factory('Statistic', ["$timeout", "$rootScope", function($timeout, $rootScope) {
        return function(statistic) {

            var self = {};

            self.show = true;

            self.changeSubstatistic = function(newSubstatistic) {
                self.show = false;

                var done = $timeout(function () {
                    self.activeSubstatistic = newSubstatistic;
                    self.show = true;
                    $rootScope.$apply();

                }, 300);

                if (newSubstatistic.display == 'barchart') {
                    done.then(function() {
                        $rootScope.$broadcast('chart:rerender');
                    });
                }
            };

            statistic.forEach(function(substatistic) {

                if (!self.substatistics) {

                    self.substatistics = [];
                    self.activeSubstatistic = substatistic;
                    self.type = substatistic.type;
                    self.camelCaseType = self.type.replace(/\W/g, "");
                }

                if (substatistic.display == 'barchart') {
                    substatistic.chartType = 'bar';
                }

                self.substatistics.push(substatistic);
            });

            return self;
        }
    }]);
})();
(function() {
    var collectionsApp = angular.module('app', []);

    collectionsApp.controller("createCollectionController", ["$scope", "collectionService", "flashMessage", function($scope, collectionService, flashMessage) {
        $scope.is = {
            creatingCollection: false,
            editingCollection: false,
            deletingCollection: false,
            mergingCollection: false
        };

        $scope.createCollection = function() {
            $scope.is.creatingCollection = true;
            collectionService.create($scope.newCollection).then(function() {
                flashMessage.addError('Your collection could not be created.');
            });
        };

        $scope.editCollection = function() {

        };

        $scope.deleteCollection = function() {

        };

        $scope.mergeCollection = function() {

        };
    }]);

    collectionsApp.service("collectionService", ["$http", function($http) {
        this.create = function(collection) {
            $http.post('/missioncontrol/collections/create', collection).then(function(response) {
                window.location.href = '/missioncontrol/collections/' + response.data.collection_id;
            }, function(response) {
                return response;
            });
        };

        this.delete = function(collection) {
            $http.delete('/missioncontrol/collections/' + collection.collection_id).then(function(response) {
                window.location.href = '/missioncontrol/collections';
            });
        };

        this.edit = function(collection) {
            return $http.patch('/missioncontrol/collections/' + collection.collection_id, collection);
        }
    }]);
})();

(function() {
    var liveApp = angular.module('app', []);

    liveApp.config(["$sceDelegateProvider", function($sceDelegateProvider) {
        $sceDelegateProvider.resourceUrlWhitelist([
            'self',
            'https://www.youtube.com/**']);
    }]);

    liveApp.controller('liveController', ["$scope", "liveService", "Section", "Resource", "Update", "$timeout", "flashMessage", function($scope, liveService, Section, Resource, Update, $timeout, flashMessage) {
        var socket = io(document.location.origin + ':3000');

        $scope.auth = laravel.auth;
        $scope.isActive = laravel.isActive;

        $scope.data = {
            upcomingMission: laravel.mission
        };

        $scope.streamSize = {
            smaller: 1,
            normal: 2,
            larger: 3
        };

        $scope.streamOption = {
            noVideo: 1,
            spacex: 2,
            spacexClean: 3,
            nasa: 4,
            spacexAndSpacexClean: 5,
            spacexAndNasa: 6,
            spacexCleanAndNasa: 7
        };

        $scope.updates = laravel.updates.map(function(update) {
            return new Update(update);
        });

        $scope.settings = {
            isGettingStarted: laravel.isActive == true ? null : false,
            getStartedHeroText: 'You are the launch controller.',
            getStarted: function() {
                this.isGettingStarted = true;
                this.getStartedHeroText = 'Awesome. We just need a bit of info first.'
            },
            isCreating: false,
            isTurningOff: false,
            turnOnSpaceXStatsLive: function() {
                $scope.settings.isCreating = true;
                liveService.create($scope.liveParameters).then(function() {
                    $scope.settings.isCreating = false;
                    $scope.isActive = true;
                    $scope.settings.isGettingStarted = false;
                });
            },
            turnOffSpaceXStatsLive: function() {
                $scope.settings.isTurningOff = true;
                liveService.destroy().then(function() {
                    $scope.isActive = $scope.auth = false;
                });
            },
            toggleForLaunch: function() {
                if ($scope.liveParameters.isForLaunch) {
                    $scope.liveParameters.reddit.title = '/r/SpaceX ' + $scope.data.upcomingMission.name + ' Official Launch Discussion & Updates Thread';
                    $scope.liveParameters.title = $scope.data.upcomingMission.name;
                    $scope.liveParameters.countdown.to = $scope.data.upcomingMission.launch_date_time;
                    $scope.liveParameters.countdown.isPaused = false;
                } else {
                    $scope.liveParameters.title = $scope.liveParameters.reddit.title = null;
                }
            },
            isEditingDetails: false,
            addSection: function() {
                if (!$scope.liveParameters.sections) {
                    $scope.liveParameters.sections = [];
                }
                $scope.liveParameters.sections.push(new Section({}));
            },
            removeSection: function(section) {
                $scope.liveParameters.sections.splice($scope.liveParameters.sections.indexOf(section), 1);
            },
            addResource: function() {
                if (!$scope.liveParameters.resources) {
                    $scope.liveParameters.resources = [];
                }
                $scope.liveParameters.resources.push(new Resource({}));
            },
            removeResource: function(resource) {
                $scope.liveParameters.resources.splice($scope.liveParameters.resources.indexOf(resource), 1);
            },
            isUpdating: false,
            updateDetails: function() {
                $scope.settings.isUpdating = true;
                liveService.updateDetails($scope.liveParameters).then(function(response) {
                    $scope.settings.isEditingDetails = $scope.settings.isUpdating = false;
                });
            },
            isPausingCountdown: false,
            pauseCountdown: function() {
                $scope.settings.isPausingCountdown = true;
                liveService.pauseCountdown().then(function() {
                    $scope.settings.isPausingCountdown = false;
                });
            },
            isResumingCountdown: false,
            resumeCountdown: function() {
                $scope.settings.isResumingCountdown = true;
                liveService.resumeCountdown($scope.liveParameters.countdown.newLaunchTime).then(function() {
                    $scope.settings.isResumingCountdown = false
                    ;
                });
            }
        };

        // Set the default parameters here
        $scope.liveParameters = {
            isForLaunch: true,
            title: laravel.title ? laravel.title : $scope.data.upcomingMission.name,
            reddit: {
                title: laravel.reddit.title ? laravel.reddit.title : '/r/SpaceX ' + $scope.data.upcomingMission.name + ' Official Launch Discussion & Updates Thread',
                thing: laravel.reddit.thing ? laravel.reddit.thing : null
            },
            countdown: {
                to: laravel.countdown.to ? laravel.countdown.to : $scope.data.upcomingMission.launch_date_time,
                isPaused: laravel.countdown.isPaused,
                newLaunchTime: null
            },
            streams: {
                spacex: {
                    isAvailable: laravel.streams.spacex ? laravel.streams.spacex.isAvailable : false,
                    youtubeVideoId: laravel.streams.spacex ? laravel.streams.spacex.youtubeVideoId : null,
                    isActive: laravel.streams.spacex ? laravel.streams.spacex.isActive : false
                },
                spacexClean: {
                    isAvailable: laravel.streams.spacexClean ? laravel.streams.spacexClean.isAvailable : false,
                    youtubeVideoId: laravel.streams.spacexClean ? laravel.streams.spacexClean.youtubeVideoId : null,
                    isActive: laravel.streams.spacexClean ? laravel.streams.spacexClean.isActive : false
                },
                nasa: {
                    isAvailable: laravel.streams.nasa ? laravel.streams.nasa.isAvailable : false,
                    youtubeVideoId: 'HDh4uK9PvJU',
                    isActive: true
                }
            },
            description: {
                raw: laravel.description.raw,
                markdown: laravel.description.markdown
            },
            sections: laravel.sections ? laravel.sections : [],
            resources: laravel.resources ? laravel.resources : [],
            status: {
                text: laravel.status.text ? laravel.status.text.replace(/([A-Z])/g, ' $1').replace(/^./, function(str){ return str.toUpperCase(); }).trim() : 'Upcoming',
                class: function() {
                    if ($scope.liveParameters.status.text) {
                        return $scope.liveParameters.status.text.toLowerCase().replace(/\s+/g, "-");
                    }
                }
            }
        };

        $scope.user = {
            streamOption: $scope.streamOption.spacex,
            streamSize: $scope.streamSize.normal,
            sanitizedStreamOption: function() {

                $scope.user.streamOption = parseInt($scope.user.streamOption);

                switch ($scope.user.streamOption) {
                    case $scope.streamOption.noVideo:               return 'No Video';
                    case $scope.streamOption.spacex:                return 'SpaceX';
                    case $scope.streamOption.spacexClean:           return 'SpaceX (Clean)';
                    case $scope.streamOption.nasa:                  return 'NASA';
                    case $scope.streamOption.spacexAndNasa:         return 'SpaceX & NASA';
                    case $scope.streamOption.spacexCleanAndNasa:    return 'SpaceX (Clean) & NASA';
                    case $scope.streamOption.spacexAndSpacexClean:  return 'SpaceX & SpaceX (Clean)';
                }
            },
            isConfiguringStreams: false,
            isWatching: {
                singleStream: function() {
                    return [$scope.streamOption.spacex, $scope.streamOption.spacexClean, $scope.streamOption.nasa]
                            .indexOf($scope.user.streamOption) !== -1;
                },
                doubleStream: function() {
                    return [$scope.streamOption.spacexAndNasa, $scope.streamOption.spacexCleanAndNasa, $scope.streamOption.spacexAndSpacexClean]
                            .indexOf($scope.user.streamOption) !== -1;
                }
            }
        };

        /*
         *
         */
        $scope.isLivestreamVisible = function() {
            if ($scope.user.streamOption == $scope.streamOption.spacex) {
                return $scope.liveParameters.streams.spacex.isActive;
            }

            if ($scope.user.streamOption == $scope.streamOption.spacexClean) {
                return $scope.liveParameters.streams.spacexClean.isActive;
            }

            if ($scope.user.streamOption == $scope.streamOption.nasa) {
                return $scope.liveParameters.streams.nasa.isActive;
            }

            if ($scope.user.streamOption == $scope.streamOption.spacexAndNasa) {
                return $scope.liveParameters.streams.nasa.isActive || $scope.liveParameters.streams.spacex.isActive;
            }

            if ($scope.user.streamOption == $scope.streamOption.spacexCleanAndNasa) {
                return $scope.liveParameters.streams.nasa.isActive || $scope.liveParameters.streams.spacexClean.isActive;
            }

            if ($scope.user.streamOption == $scope.streamOption.spacexAndSpacexClean) {
                return $scope.liveParameters.streams.spacex.isActive || $scope.liveParameters.streams.spacexClean.isActive;
            }
        };

        /*
         * Checks if any livestream is visible. We don't need to check for the existence of the spacexClean stream.
         */
        $scope.isAnyStreamAvailable = function() {
            return $scope.liveParameters.streams.spacex.isActive === true ||$scope.liveParameters.streams.nasa.isActive === true;
        };

        $scope.send = {
            new: {
                message: null,
                messageType: null
            },

            /*
             * Send a launch update (message) via POST off to the server to be broadcast to everyone else
             */
            message: function(form) {
                // Send the message
                liveService.sendMessage({
                    message: $scope.send.new.message,
                    messageType: null
                }).then(function() {
                    flashMessage.addOK('Update submitted');
                });

                // Reset the form
                $scope.send.new.message = "";
                form.$setUntouched();
            }
        };

        $scope.buttons = {
            cannedResponses: {
                holdAbort: laravel.cannedResponses ? laravel.cannedResponses.holdAbort : null,
                terminalCount: laravel.cannedResponses ? laravel.cannedResponses.terminalCount : null,
                inProgress: laravel.cannedResponses ? laravel.cannedResponses.inProgress : null,
                maxQ: laravel.cannedResponses ? laravel.cannedResponses.maxQ : null,
                MECO: laravel.cannedResponses ? laravel.cannedResponses.MECO : null,
                stageSep: laravel.cannedResponses ? laravel.cannedResponses.stageSep : null,
                mVacIgnition: laravel.cannedResponses ? laravel.cannedResponses.mVacIgnition : null,
                SECO: laravel.cannedResponses ? laravel.cannedResponses.SECO : null,
                missionSuccess: laravel.cannedResponses ? laravel.cannedResponses.missionSuccess : null,
                missionFailure: laravel.cannedResponses ? laravel.cannedResponses.missionFailure : null
            },
            isUnlocked: {},
            click: function(messageType, form) {
                // If the button has been clicked in the last 5 seconds, we should send the message
                if ($scope.buttons.isUnlocked[messageType]) {

                    liveService.sendMessage({
                        message: $scope.send.new.message,
                        messageType: messageType
                    }).then(function() {
                        flashMessage.addOK('Canned update submitted');
                    });

                    // Reset the form
                    $scope.send.new.message = "";
                    form.$setUntouched();

                // The button hasn't been clicked recently, make it active instead
                } else {
                    $scope.buttons.isUnlocked[messageType] = true;
                    $scope.send.new.message = $scope.buttons.cannedResponses[messageType];

                    $timeout(function() {
                        $scope.send.new.message = "";
                        $scope.buttons.isUnlocked[messageType] = false;
                    }, 1500);
                }
            },
            isUpdatingCannedResponses: false,
            updateCannedResponses: function() {
                $scope.buttons.isUpdatingCannedResponses = true;
                liveService.updateCannedResponses($scope.buttons.cannedResponses).then(function(response) {
                    $scope.buttons.isUpdatingCannedResponses = false;
                });
            }
        };

        // Callback executed by countdown directive
        $scope.setTimeBetweenNowAndLaunch = function(relativeSecondsBetween) {
            $scope.timeBetweenNowAndLaunch = relativeSecondsBetween;
        };

        // Websocket listeners
        socket.on('live-updates:SpaceXStats\\Events\\Live\\LiveStartedEvent', function(data) {
            console.log(data);
            $scope.isActive = true;
            $scope.liveParameters.description = data.data.description;
            $scope.liveParameters.sections = data.data.sections;
            $scope.liveParameters.resources = data.data.resources;
            $scope.liveParameters.title = data.data.title;
            $scope.liveParameters.reddit = data.data.reddit;
            $scope.liveParameters.streams = data.data.streams;
            $scope.liveParameters.countdown = data.data.countdown;
            $scope.liveParameters.status.text = data.data.status;
            if ($scope.auth) {
                $scope.buttons.cannedResponses = data.data.cannedResponses;
            }
            $scope.$apply();
        });

        socket.on('live-updates:SpaceXStats\\Events\\Live\\LiveCountdownEvent', function(data) {
            console.log(data);
            // Countdown is being resumed
            if (data.newLaunchTime != null) {
                $scope.liveParameters.countdown = {
                    isPaused: false,
                    to: data.newLaunchTime,
                    newLaunchDate: data.newLaunchTime
                };

            // Countdown is being paused
            } else {
                $scope.liveParameters.countdown.isPaused = true;
            }
            $scope.$apply();
        });

        socket.on('live-updates:SpaceXStats\\Events\\Live\\LiveUpdateCreatedEvent', function(data) {
            $scope.updates.push(new Update(data.liveUpdate));
            if (["upcoming", "holdAbort", "terminalCount", "inProgress", "missionSuccess", "missionFailure"].indexOf(data.liveUpdate.updateType) !== -1) {
                // Take the camelCased update type and transform it to Human Readable Case
                $scope.liveParameters.status.text = data.liveUpdate.updateType.replace(/([A-Z])/g, ' $1').replace(/^./, function(str){ return str.toUpperCase(); });
            }
            $scope.$apply();
        });

        socket.on('live-updates:SpaceXStats\\Events\\Live\\LiveUpdateUpdatedEvent', function(data) {
            var indexOfUpdate = $scope.updates.indexOf($scope.updates.filter(function(update) {
                return update.id == data.liveUpdate.id;
            }).shift());

            $scope.updates[indexOfUpdate] = new Update(data.liveUpdate);
            $scope.$apply();
        });

        socket.on('live-updates:SpaceXStats\\Events\\Live\\LiveEndedEvent', function() {
            $scope.isActive = false;
            $scope.$apply();
        });

        socket.on('live-updates:SpaceXStats\\Events\\Live\\LiveDetailsUpdatedEvent', function(data) {
            console.log(data);
        });

        socket.on('live-updates:SpaceXStats\\Events\\WebcastStartedEvent', function(data) {
            console.log(data);
            for (var stream in data.videos) {
                if (data.videos.hasOwnProperty(stream)) {
                    $scope.liveParameters.streams[stream].isActive = true;
                    $scope.liveParameters.streams[stream].isAvailable = true;
                    $scope.liveParameters.streams[stream].youtubeVideoId = data.videos[stream];
                }
            }

            $scope.$apply();
        });

        socket.on('live-updates:SpaceXStats\\Events\\WebcastEndedEvent', function(data) {
             console.log(data);
        });
    }]);

    liveApp.service('liveService', ["$http", function($http) {

        this.sendMessage = function(message) {
            return $http.post('/live/send/message', message);
        };

        this.editMessage = function(message) {
            return $http.patch('/live/send/message', message);
        };

        this.pauseCountdown = function() {
            return $http.patch('live/send/countdown/pause');
        };

        this.resumeCountdown = function(data) {
            return $http.patch('live/send/countdown/resume', { newLaunchDate: data});
        };

        this.updateDetails = function(details) {
            return $http.patch('/live/send/details', details);
        };

        this.updateCannedResponses = function(cannedResponses) {
            return $http.patch('/live/send/cannedresponses', { cannedResponses: cannedResponses });
        };

        this.create = function(createThreadParameters) {
            return $http.post('/live/send/create', createThreadParameters);
        };

        this.destroy = function() {
            return $http.delete('/live/send/destroy');
        };
    }]);

    liveApp.factory('Update', ['liveService', function(liveService) {
        return function(update) {
            var self = update;

            self.isEditFormVisible = false;
            self.isEditButtonDisabled = false;

            self.edit = function() {
                self.isEditButtonDisabled = true;
                liveService.editMessage(self).then(function() {
                    self.isEditFormVisible = self.isEditButtonDisabled = false;
                });
            };

            return self;
        }
    }]);

    liveApp.factory('Resource', function() {
        return function() {
            this.title = null;
            this.url = null;
            this.courtesy = null;
        }
    });

    liveApp.factory('Section', function() {
        return function() {
            this.title = null;
            this.content = null;
        }
    });
})();
(function() {
    var signUpApp = angular.module('app', ['ngAnimate']);

    signUpApp.controller("signUpController", ["$scope", "signUpService", "flashMessage", function($scope, signUpService, flashMessage) {
        $scope.hasSignedUp = false;
        $scope.isSigningUp = false;
        $scope.signUpButtonText = "Sign Up";

        $scope.signUp = function() {
            $scope.isSigningUp = true;
            $scope.signUpButtonText = "Signing Up...";

            signUpService.go($scope.user).then(function(response) {
                $scope.hasSignedUp = true;
                $scope.isSigningUp = false;
            }, function(response) {
                // Otherwise show error
                $scope.isSigningUp = false;
                $scope.signUpButtonText = "Sign Up";
                flashMessage.addError('Your account could not be created. Please contact us.');
				console.log(response);
            });
        }
    }]);

    signUpApp.service("signUpService", ["$http", function($http) {
        this.go = function(credentials) {
            return $http.post('/auth/signup', credentials);
        };
    }]);
})();
(function() {
    var userApp = angular.module('app', []);

    userApp.controller("editUserController", ['$http', '$scope', 'editUserService', 'flashMessage', function($http, $scope, editUserService, flashMessage) {
        $scope.isUpdating = {
            profile: false,
            emailNotifications: false,
            SMSNotifications: false
        };

        $scope.username = laravel.user.username;

        $scope.missions = laravel.missions;

        $scope.patches = laravel.patches;

        $scope.profile = {
            summary: laravel.user.profile.summary,
            twitter_account: laravel.user.profile.twitter_account,
            reddit_account: laravel.user.profile.reddit_account,
            favorite_quote: laravel.user.profile.favorite_quote,
            favorite_mission: laravel.user.profile.favorite_mission,
            favorite_patch: laravel.user.profile.favorite_patch
        };

        $scope.updateProfile = function() {
            $scope.isUpdating.profile = true;
            $http.patch('/users/' + $scope.username + '/edit', $scope.profile)
                .then(function(response) {
                    window.location = '/users/' + $scope.username;
                });
        };

        $scope.emailNotifications = {
            LaunchChange: laravel.notifications.LaunchChange,
            NewMission: laravel.notifications.NewMission,
            TMinus24HoursEmail: laravel.notifications.TMinus24HoursEmail,
            TMinus3HoursEmail: laravel.notifications.TMinus3HoursEmail,
            TMinus1HourEmail: laravel.notifications.TMinus1HourEmail,
            NewsSummaries: laravel.notifications.NewsSummaries
        };

        $scope.updateEmailNotifications = function() {
            $scope.isUpdating.emailNotifications = true;
            editUserService.updateEmails($scope.username, $scope.emailNotifications).then(function() {
                $scope.isUpdating.emailNotifications = false;
            });
        };

        $scope.SMSNotification = {
            mobile: laravel.user.mobile
        };

        if (laravel.notifications.TMinus24HoursSMS === true) {
            $scope.SMSNotification.status = "TMinus24HoursSMS";
        } else if (laravel.notifications.TMinus3HoursSMS === true) {
            $scope.SMSNotification.status = "TMinus3HoursSMS";
        } else if (laravel.notifications.TMinus1HourSMS === true) {
            $scope.SMSNotification.status = "TMinus1HourSMS";
        } else {
            $scope.SMSNotification.status = "false";
        }

        $scope.updateSMSNotifications = function() {
            $scope.isUpdating.SMSNotifications = true;
            editUserService.updateSMS($scope.username, $scope.SMSNotification).then(function() {
                $scope.isUpdating.SMSNotifications = false;
            });
        }

    }]);

    userApp.service('editUserService', ["$http", "flashMessage", function($http, flashMessage) {
        this.updateSMS = function(username, notification) {
            return $http.patch('/users/' + username + '/edit/smsnotifications',

                { 'SMSNotification': notification }

            ).then(function(response) {
                return flashMessage.addOK(response.data);
            }, function(response) {
                return flashMessage.addError(response.data);
            });
        };

        this.updateEmails = function(username, notification) {
            return $http.patch('/users/' + username + '/edit/emailnotifications',

                { 'emailNotifications': notification }

            ).then(function(response) {
                return flashMessage.addOK(response.data);
            }, function(response) {
                return flashMessage.addError(response.data);
            });
        };

        this.updateProfile = function() {

        };
    }]);

})();
(function() {
    var locationsApp = angular.module('app', []);

    locationsApp.controller("locationsController", ["$scope", "locationsService", "$compile", function($scope, locationsService, $compile) {

        $scope.filters = {
            noLongerUsed: true,
            launchSites: true,
            landingSites: true,
            ASDSs: true
        };

        $scope.locationPageTitle = function() {
            if (angular.isUndefined($scope.selectedLocation)) {
                return 'Locations';
            } else {
                return $scope.selectedLocation.name;
            }
        };

        $scope.isShowingMap = true;
        $scope.circleRadius = 20;

        $scope.makeLarger = function(d, i, self) {
            d3.select(self).transition()
                .duration(300)
                .attr('transform', "scale(1.5,1.5)");
        };

        $scope.makeSmaller = function(d, i, self) {
            d3.select(self).transition()
                .duration(300)
                .attr('transform', "scale(1,1)");
        };

        $scope.selectLocation = function(d) {
            $scope.selectedLocation = d;
            history.replaceState('', document.title, '#' + d.name.toLowerCase().replace(/\s/g, "-"));
            $scope.$apply();
        };

        (function() {
            locationsService.getLocationData().then(function(response) {
                $scope.locations = response.data;

                // If a hash already exists, preset it:
                if (window.location.hash) {
                    $scope.selectedLocation = $scope.locations.filter(function(location) {
                        return location.name.toLowerCase().replace(/\s/g, "-") == window.location.hash.substring(1);
                    })[0];
                }

                var svg = d3.select('svg');

                var xScale = d3.scale.linear().domain([0, 1400])
                    .range([0,parseInt(svg.style('width'))]);

                var yScale = d3.scale.linear().domain([0, 700])
                    .range([0,parseInt(svg.style('height'))]);

                var colorScale = d3.scale.ordinal().domain(['Launch Site', 'Landing Site', 'ASDS', 'Facility'])
                    .range(['#CCAC55', '#40C085', '#4050C0', '#21272B']);

                var iconScale;

                var placeOnMap = function(idOfDataPoint) {
                    switch (idOfDataPoint) {
                        case 1:
                            return {x: 201, y: 550 };
                        case 2:
                            return {x: 1009, y: 525 };
                        case 3:
                            return {x: 274, y: 380 };
                        case 4:
                            return {x: 1008, y: 485 };
                        case 5:
                            return {x: 684, y: 601 };
                        case 6:
                            return {x: 1100, y: 550 };
                        case 7:
                            return {x: 1009, y: 565 };
                        case 8:
                            return {x: 274, y: 340 };
                        case 9:
                            return {x: 1100, y: 500 };
                        case 10:
                            return {x: 1000, y: 550 };
                    }
                    return { x: 0, y: 0 };
                };

                // Grab the selection of elements and data
                var enterSelection = svg.selectAll("circle").data($scope.locations, function(d) { return d.name; }).enter();

                // Apply a grouping with an overall translation
                var grouping = enterSelection.append("g")
                    .attr('class', function(d) { return 'location ' + d.name; })
                    .attr('transform', function(d) {
                        return 'translate(' + placeOnMap(d.location_id).x + ',' + placeOnMap(d.location_id).y + ')';
                    });

                grouping.insert('circle')
                    .attr('fill', function(d) { return colorScale(d.type); })
                    .attr('r', $scope.circleRadius)
                    .on('mouseover', function(d, i) {
                        $scope.makeLarger(d, i, this);
                    })
                    .on('mouseout', function(d, i) {
                        $scope.makeSmaller(d, i, this);
                    })
                    .on('click', function(d, i) {
                        $scope.selectLocation(d, i, this);
                    })
                    .call(function(){
                        $compile(this[0].parentNode)($scope);
                    });

            });
        })();
    }]);

    locationsApp.service("locationsService", ["$http", function($http) {
        this.getLocationData = function() {
            return $http.get('/locations/getLocationData');
        }
    }]);
})();

// Original jQuery countdown timer written by /u/EchoLogic, improved and optimized by /u/booOfBorg.
// Rewritten as an Angular directive for SpaceXStats 4
(function() {
    var app = angular.module('app');

    app.directive('countdown', ['$interval', function($interval) {
        return {
            restrict: 'E',
            scope: {
                countdownTo: '=',
                specificity: '=?',
                type: '@',
                isPaused: '=?',
                isVisibleWhenPaused: '=?',
                callback: '&?'
            },
            link: function($scope, elem, attrs) {

                if (attrs.callback) {
                    $scope.callback = $scope.callback();
                }

                $scope.isPaused = typeof $scope.isPaused !== 'undefined' ? $scope.isPaused : false;
				$scope.isPaused = $scope.isPaused == "0" ? false : $scope.isPaused;
                $scope.isVisibleWhenPaused = typeof $scope.isVisibleWhenPaused !== 'undefined' ? $scope.isVisibleWhenPaused : false;

                $scope.isLaunchExact = angular.isUndefined($scope.specificity) || $scope.specificity == 6 || $scope.specificity == 7;

                var splitSeconds = function(seconds) {
                    // Calculate the number of days, hours, minutes, seconds
                    $scope.days = Math.floor(seconds / (60 * 60 * 24));
                    seconds -= $scope.days * 60 * 60 * 24;

                    $scope.hours = Math.floor(seconds / (60 * 60));
                    seconds -= $scope.hours * 60 * 60;

                    $scope.minutes = Math.floor(seconds / 60);
                    seconds -= $scope.minutes * 60;

                    $scope.seconds = seconds;

                    $scope.daysText = $scope.days == 1 ? 'Day' : 'Days';
                    $scope.hoursText = $scope.hours == 1 ? 'Hour' : 'Hours';
                    $scope.minutesText = $scope.minutes == 1 ? 'Minute' : 'Minutes';
                    $scope.secondsText = $scope.seconds == 1 ? 'Second' : 'Seconds';
                };

                var countdownProcessor = function() {

                    if (!$scope.isPaused) {
                        var relativeSecondsBetween = moment.utc().diff(moment.utc($scope.countdownTo, 'YYYY-MM-DD HH:mm:ss'), 'second');
                        var secondsBetween = Math.abs(relativeSecondsBetween);

                        $scope.sign = relativeSecondsBetween <= 0 ? '-' : '+';
                        $scope.tMinusZero = secondsBetween == 0;

                        splitSeconds(secondsBetween);

                        if (attrs.callback) {
                            $scope.callback(relativeSecondsBetween);
                        }
                    }
                };

                // Countdown here
                if ($scope.isLaunchExact && $scope.type != 'interval') {
                    $interval(countdownProcessor, 1000);
                } else if ($scope.type == 'interval') {
                    splitSeconds($scope.countdownTo);
                } else {
                    $scope.countdownText = $scope.countdownTo;
                }
            },
            templateUrl: '/js/templates/countdown.html'
        }
    }]);
})();
// Original jQuery countdown timer written by /u/EchoLogic, improved and optimized by /u/booOfBorg.
// Rewritten as an Angular directive for SpaceXStats 4
(function() {
    var app = angular.module('app');

    app.directive('launchDate', ['$interval', '$filter', function($interval, $filter) {
        return {
            restrict: 'E',
            scope: {
                launchSpecificity: '=',
                launchDateTime: '='
            },
            link: function($scope, elem, attrs) {
                /*
                 *   Timezone stuff.
                 */
                // Get the IANA Timezone identifier and format it into a 3 letter timezone.
                $scope.localTimezone = moment().tz(jstz.determine().name()).format('z');

                // Set the format to be displayed based on the launch specificity
                switch ($scope.launchSpecificity) {
					case "6":
                    case 6:
                        $scope.currentFormat = 'MMMM d, yyyy';
                        break;
					case "7":
                    case 7:
                        $scope.currentFormat = 'h:mm:ssa MMMM d, yyyy';
                        break;
                    default:
                        $scope.currentFormat = null;
                }

                $scope.currentTimezone = null;
                $scope.currentTimezoneFormatted = "Local ("+ $scope.localTimezone +")";

                $scope.setTimezone = function(timezoneToSet) {
                    if (timezoneToSet === 'local') {
                        $scope.currentTimezone = null;
                        $scope.currentTimezoneFormatted = "Local ("+ $scope.localTimezone +")";
                    } else if (timezoneToSet === 'ET') {
                        $scope.currentTimezone = moment().tz("America/New_York").format('z');
                        $scope.currentTimezoneFormatted = 'Eastern';
                    } else if (timezoneToSet === 'PT') {
                        $scope.currentTimezone = moment().tz("America/Los_Angeles").format('z');
                        $scope.currentTimezoneFormatted = 'Pacific';
                    } else {
                        $scope.currentTimezoneFormatted = $scope.currentTimezone = 'UTC';
                    }
                };

                $scope.isHoveringOverAlert = false;

                $scope.hoveringOverAlert = function() {
                    $scope.isHoveringOverAlert = !$scope.isHoveringOverAlert;
                };

                $scope.displayDateTime = function() {
                    //if ($scope.isHoveringOverAlert) {
                    //    return 'This launch has no time yets';
                    //}
                    if ($scope.launchSpecificity >= 6) {
                        return $filter('date')(moment.utc($scope.launchDateTime, 'YYYY-MM-DD HH:mm:ss').toDate(), $scope.currentFormat, $scope.currentTimezone);
                    } else {
                        return $scope.launchDateTime;
                    }

                };
            },
            templateUrl: '/js/templates/launchDate.html'
        }
    }]);
})();
(function() {
    var app = angular.module('app');

    app.directive('upload', ['$parse', function($parse) {
        return {
            restrict: 'A',
            link: function($scope, element, attrs) {

                // Initialize the dropzone
                var dropzone = new Dropzone(element[0], {
                    url: attrs.action,
                    autoProcessQueue: false,
                    dictDefaultMessage: "Upload files here!",
                    maxFilesize: 1024, // MB
                    addRemoveLinks: true,
                    uploadMultiple: attrs.multiUpload,
                    parallelUploads: 5,
                    maxFiles: 5,
                    successmultiple: function(dropzoneStatus, files) {

                        $scope.files = files.objects;

                        // Run a callback function with the files passed through as a parameter
                        if (typeof attrs.callback !== 'undefined' && attrs.callback !== "") {
                            var func = $parse(attrs.callback);
                            func($scope, { files: files });
                        }
                    },
                    error: function() {
                        $scope.isUploading = false;
                    }
                });

                dropzone.on("addedfile", function(file) {
                    ++$scope.queuedFiles;
                    $scope.$apply();
                });

                dropzone.on("removedfile", function(file) {
                    --$scope.queuedFiles;
                    $scope.$apply();
                });

                // upload the files
                $scope.uploadFiles = function() {
                    $scope.isUploading = true;
                    dropzone.processQueue();
                }
            }
        }
    }]);
})();
(function() {
    var app = angular.module('app', []);

    app.directive("tags", ["Tag", "$timeout", function(Tag, $timeout) {
        return {
            require: 'ngModel',
            replace: true,
            restrict: 'E',
            scope: {
                availableTags: '=',
                currentTags: '=ngModel'
            },
            link: function($scope, element, attributes, ctrl) {
                $scope.suggestions = [];
                $scope.inputWidth = {};
                $scope.currentTags = typeof $scope.currentTags !== 'undefined' ? $scope.currentTags : [];

                ctrl.$options = {
                    allowInvalid: true
                };

                $scope.createTag = function(createdTag) {
                    if ($scope.currentTags.length == 5 || angular.isUndefined(createdTag)) {
                        return;
                    }

                    var tagIsPresentInCurrentTags = $scope.currentTags.filter(function(tag) {
                        return tag.name == createdTag;
                    });

                    if (createdTag.length > 0 && tagIsPresentInCurrentTags.length === 0) {

                        // check if tag is present in the available tags array
                        var tagIsPresentInAvailableTags = $scope.availableTags.filter(function(tag) {
                            return tag.name == createdTag;
                        });

                        // Either fetch the tag from the current list of tags or create
                        var newTag = tagIsPresentInAvailableTags.length === 1 ? tagIsPresentInAvailableTags[0] : new Tag({ id: null, name: createdTag, description: null });

                        $scope.currentTags.push(newTag);

                        // reset the input field
                        $scope.tagInput = "";

                        $scope.updateSuggestionList();
                        $scope.updateInputLength();
                    }
                };

                $scope.removeTag = function(removedTag) {
                    $scope.currentTags.splice($scope.currentTags.indexOf(removedTag), 1);
                    $scope.updateSuggestionList();
                    $scope.updateInputLength();
                };

                $scope.tagInputKeydown = function(event) {
                    // Currently using jQuery.event.which to detect keypresses, keyCode is deprecated, use KeyboardEvent.key eventually:
                    // https://developer.mozilla.org/en-US/docs/Web/API/KeyboardEvent/key

                    // event.key == ' ' || event.key == 'Enter'
                    if (event.which == 32 || event.which == 13) {
                        event.preventDefault();

                        $scope.createTag($scope.tagInput);

                        // event.key == 'Backspace'
                    } else if (event.which == 8 && $scope.tagInput == "") {
                        event.preventDefault();

                        // grab the last tag to be inserted (if any) and put it back in the input
                        if ($scope.currentTags.length > 0) {
                            $scope.tagInput = $scope.currentTags.pop().name;
                        }
                    }
                };

                $scope.updateInputLength = function() {
                    $timeout(function() {
                        $scope.inputLength = $(element).find('.wrapper').innerWidth() - $(element).find('.tag-wrapper').outerWidth() - 1;
                    });
                };

                $scope.areSuggestionsVisible = false;
                $scope.toggleSuggestionVisibility = function() {
                    $scope.areSuggestionsVisible = $scope.currentTags.length  < 5 ? !$scope.areSuggestionsVisible : false;
                };

                $scope.updateSuggestionList = function() {
                    var search = new RegExp($scope.tagInput, "i");

                    $scope.suggestions = $scope.availableTags.filter(function(availableTag) {
                        if ($scope.currentTags.filter(function(currentTag) {
                                return availableTag.name == currentTag.name;
                            }).length == 0) {
                            return search.test(availableTag.name);
                        }
                        return false;
                    }).slice(0,6);
                };

                ctrl.$validators.taglength = function(modelValue, viewValue) {
                    return viewValue.length > 0 && viewValue.length < 6;
                };

                $scope.$watch('currentTags', function() {
                    ctrl.$validate();
                }, true);

            },
            templateUrl: '/js/templates/tags.html'
        }
    }]);

    app.factory("Tag", function() {
        return function(tag) {
            var self = tag;

            // Convert the tag to lowercase and replace all spaces present.
            self.name = tag.name.toLowerCase().replace(/[^a-z0-9-]/g, "").substring(0, 50);

            return self;
        }
    });
})();


(function() {
    var app = angular.module('app');

    app.directive('missionCard', function() {
        return {
            restrict: 'E',
            replace: true,
            scope: {
                mission: '='
            },
            link: function($scope) {
            },
            templateUrl: '/js/templates/missionCard.html'
        }
    });
})();
(function() {
    var app = angular.module('app');

    app.directive('deltaV', function() {
        return {
            restrict: 'E',
            scope: {
                object: '=ngModel',
                hint: '@'
            },
            link: function($scope, element, attributes) {

                $scope.constants = {
                    SECONDS_PER_DAY: 86400,
                    DELTAV_TO_DAY_CONVERSION_RATE: 1000
                };

                var baseTypeScores = {
                    Image: 10,
                    GIF: 10,
                    Audio: 20,
                    Video: 20,
                    Document: 20,
                    Tweet: 5,
                    Article: 10,
                    Comment: 5,
                    Webpage: 10,
                    Text: 10
                };

                var specialTypeMultiplier = {
                    "Mission Patch": 2,
                    "Photo": 1.1,
                    "Launch Video": 2,
                    "Press Kit": 2,
                    "Weather Forecast": 2,
                    "Press Conference": 1.5
                };

                var resourceQuality = {
                    multipliers: {
                        perMegapixel: 5,
                        perMinute: 2
                    },
                    scores: {
                        perPage: 2
                    }
                };

                var metadataScore = {
                    summary: {
                        perCharacter: 0.02
                    },
                    author: {
                        perCharacter: 0.2
                    },
                    attribution: {
                        perCharacter: 0.1
                    },
                    tags: {
                        perTag: 1
                    }
                };

                var dateAccuracyMultiplier = {
                    year: 1,
                    month: 1.05,
                    date: 1.1,
                    datetime: 1.2
                };

                var dataSaverMultiplier = {
                    hasExternalUrl: 2
                };

                var originalContentMultiplier = {
                    isOriginalContent: 1.5
                };

                $scope.$watch("object", function(object) {
                    if (typeof object !== 'undefined') {
                        var calculatedValue = $scope.calculate(object);
                        $scope.setCalculatedValue(calculatedValue);
                    }
                }, true);

                $scope.calculate = function(object) {
                    var internalValue = 0;

                    // typeRegime
                    internalValue += baseTypeScores[$scope.hint];

                    // specialTypeRegime
                    if (object.subtype !== null) {
                        if (object.subtype in specialTypeMultiplier) {
                            internalValue *= specialTypeMultiplier[object.subtype];
                        }
                    }

                    // resourceQualityRegime
                    switch ($scope.hint) {
                        case 'Image':
                            internalValue += subscores.megapixels(object);
                            break;

                        case 'GIF':
                            internalValue += subscores.megapixels(object) * subscores.minutes(object);
                            break;

                        case 'Video':
                            internalValue += subscores.megapixels(object) * subscores.minutes(object);
                            break;

                        case 'Audio':
                            internalValue += subscores.minutes(object);
                            break;

                        case 'Document':
                            internalValue += subscores.pages(object);
                            break;
                    }

                    // metadataRegime
                    if (object.summary) {
                        internalValue += object.summary.length * metadataScore.summary.perCharacter;
                    }
                    if (object.author) {
                        internalValue += object.author.length * metadataScore.author.perCharacter;
                    }
                    if (object.attribution) {
                        internalValue += object.attribution.length * metadataScore.attribution.perCharacter;
                    }
                    if (object.tags) {
                        internalValue += object.tags.length * metadataScore.tags.perTag;
                    }

                    // dateAccuracyRegime
                    if (object.originated_at) {
                        var month = object.originated_at.substr(5, 2);
                        var date = object.originated_at.substr(8, 2);
                        var datetime = object.originated_at.substr(11, 8);

                        if (datetime !== '00:00:00' && datetime !== '') {
                            internalValue *= dateAccuracyMultiplier.datetime;
                        } else if (date !== '00') {
                            internalValue *= dateAccuracyMultiplier.date;
                        } else if (month !== '00') {
                            internalValue *= dateAccuracyMultiplier.month;
                        } else {
                            internalValue *= dateAccuracyMultiplier.year;
                        }
                    }

                    // dataSaverRegime
                    if (object.external_url) {
                        internalValue *= dataSaverMultiplier.hasExternalUrl;
                    }

                    // originalContentRegime
                    if (object.original_content === true) {
                        internalValue *= originalContentMultiplier.isOriginalContent;
                    }

                    return Math.round(internalValue);
                };

                $scope.setCalculatedValue = function(calculatedValue) {
                    $scope.calculatedValue.deltaV = calculatedValue;
                    var seconds = Math.round($scope.calculatedValue.deltaV * ($scope.constants.SECONDS_PER_DAY / $scope.constants.DELTAV_TO_DAY_CONVERSION_RATE));
                    $scope.calculatedValue.time = seconds + ' seconds';
                };

                $scope.calculatedValue = {
                    deltaV: 0,
                    time: 0
                };

                var subscores = {
                    megapixels: function(object) {
                        if (object.dimension_width && object.dimension_height) {
                            var megapixels = (object.dimension_width * object.dimension_height) / 1000000;
                            return resourceQuality.multipliers.perMegapixel * megapixels;
                        }
                        return 0;
                    },
                    minutes: function(object) {
                        if (object.duration) {
                            return resourceQuality.multipliers.perMinute * (object.duration / 60);
                        }
                        return 0;
                    },
                    pages: function(object) {
                        if (object.page_count) {
                            return resourceQuality.scores.perPage * object.page_count;
                        }
                        return 0;
                    }
                }
            },
            templateUrl: '/js/templates/deltaV.html'
        }
    });
})();
(function() {
    var app = angular.module('app');

    app.directive('tweet', ["$http", function($http) {
        return {
            restrict: 'E',
            scope: {
                tweet: '='
            },
            link: function($scope, element, attributes, ngModelCtrl) {

                $scope.retrieveTweet = function() {

                    // Check that the entered URL contains 'twitter' before sending a request (perform more thorough validation serverside)
                    if (typeof $scope.tweet.external_url !== 'undefined' && $scope.tweet.external_url.indexOf('twitter.com') !== -1) {

                        var explodedVals = $scope.tweet.external_url.split('/');
                        var id = explodedVals[explodedVals.length - 1];

                        $http.get('/missioncontrol/create/retrievetweet?id=' + id).then(function(response) {
                            // Set parameters
                            $scope.tweet.tweet_id = id;
                            $scope.tweet.tweet_text = response.data.text;
                            $scope.tweet.tweet_user_profile_image_url = response.data.user.profile_image_url.replace("_normal", "");
                            $scope.tweet.tweet_screen_name = response.data.user.screen_name;
                            $scope.tweet.tweet_user_name = response.data.user.name;
                            $scope.tweet.originated_at = moment(response.data.created_at, 'dddd MMM DD HH:mm:ss Z YYYY').utc().format('YYYY-MM-DD HH:mm:ss');

                        });
                    } else {
                        $scope.tweet = {};
                    }

                    if (angular.isDefined($scope.tweet.external_url)) {
                        $scope.tweetRetrievedFromUrl = $scope.tweet.external_url.indexOf('twitter.com') !== -1;
                    } else {
                        $scope.tweetRetrievedFromUrl = false;
                    }
                }
            },
            templateUrl: '/js/templates/tweet.html'
        }
    }]);
})();
(function() {
    var app = angular.module('app');

    app.directive('datetime', function() {
        return {
            require: 'ngModel',
            restrict: 'E',
            scope: {
                type: '@',
                datetimevalue: '=ngModel',
                startYear: '@',
                isNull: '=',
                disabled: '=?ngDisabled'
            },
            link: function($scope, element, attrs, ngModelController) {

                $scope.days = [];
                $scope.days.push({ value: 0, display: '-'});

                for (i = 1; i <= 31; i++) {
                    $scope.days.push({ value: i, display: i });
                }

                $scope.months = [
                    { value: 0, display: '-'},
                    { value: 1, display: 'January'},
                    { value: 2, display: 'February'},
                    { value: 3, display: 'March'},
                    { value: 4, display: 'April'},
                    { value: 5, display: 'May'},
                    { value: 6, display: 'June'},
                    { value: 7, display: 'July'},
                    { value: 8, display: 'August'},
                    { value: 9, display: 'September'},
                    { value: 10, display: 'October'},
                    { value: 11, display: 'November'},
                    { value: 12, display: 'December'}
                ];

                $scope.years = function() {
                    var years = [];

                    var currentYear = moment().year();
                    var startYear = angular.isDefined($scope.startYear) ? $scope.startYear : 1950;

                    while (currentYear >= startYear) {
                        years.push(currentYear);
                        currentYear--;
                    }
                    return years;
                };

                //convert data from view format to model format
                ngModelController.$parsers.push(function(viewvalue) {

                    if ($scope.isNull == true) {
                        return null;
                    }

                    if (typeof data !== 'undefined' && moment(viewvalue).isValid()) {

                        if ($scope.type == 'datetime') {
                            var value = moment({
                                year: viewvalue.year,
                                month: viewvalue.month - 1,
                                date: viewvalue.date,
                                hour: viewvalue.hour,
                                minute: viewvalue.minute,
                                second: viewvalue.second
                            }).format('YYYY-MM-DD HH:mm:ss');

                        } else if ($scope.type == 'date') {
                            var value = moment({
                                year: viewvalue.year,
                                month: viewvalue.month - 1,
                                date: viewvalue.date
                            }).format('YYYY-MM-DD');
                        }
                    } else {

                        if ($scope.type == 'datetime') {
                            var value = viewvalue.year + "-"
                                + ("0" + viewvalue.month).slice(-2) + "-"
                                + ("0" + viewvalue.date).slice(-2) + " "
                                + ("0" + viewvalue.hour).slice(-2) + ":"
                                + ("0" + viewvalue.minute).slice(-2) + ":"
                                + ("0" + viewvalue.second).slice(-2);

                        } else {
                            var value = viewvalue.year + "-"
                                + ("0" + viewvalue.month).slice(-2) + "-"
                                + ("0" + viewvalue.date).slice(-2);
                        }
                    }
                    return value;
                });

                //convert data from model format to view format
                ngModelController.$formatters.push(function(data) {

                    // If the value is not undefined and the value is valid,
                    if (typeof data !== 'undefined' && moment(data).isValid()) {

                        var dt = moment(data);

                        if ($scope.type == 'datetime') {
                            return {
                                year: dt.year(),
                                month: dt.month() + 1,
                                date: dt.date(),
                                hour: dt.hour(),
                                minute: dt.minute(),
                                second: dt.second()
                            }
                        } else if ($scope.type == 'date') {
                            return {
                                year: dt.year(),
                                month: dt.month() + 1,
                                date: dt.date()
                            }
                        }
                    } else {

                        if ($scope.type == 'datetime') {
                            return {
                                year: moment().year(),
                                month: 0,
                                date: 0,
                                hour: 0,
                                minute: 0,
                                second: 0
                            }
                        } else if ($scope.type == 'date') {
                            return {
                                year: moment().year(),
                                month: 0,
                                date: 0
                            }
                        }
                    }
                });

                ngModelController.$render = function() {
                    $scope.year = ngModelController.$viewValue.year;
                    $scope.month = ngModelController.$viewValue.month;
                    $scope.date = ngModelController.$viewValue.date;

                    if ($scope.type == 'datetime') {
                        $scope.hour = ngModelController.$viewValue.hour;
                        $scope.minute = ngModelController.$viewValue.minute;
                        $scope.second = ngModelController.$viewValue.second;
                    }
                };

                $scope.dateIsComplete = function() {
                    return $scope.month !== 0 && $scope.date !== 0;
                };

                $scope.$watch('datetimevalue', function(value) {
                    if (typeof value === null) {
                        $scope.isNull = true;
                    }
                });

                $scope.$watch('year + month + date + hour + minute + second + isNull', function() {
                    ngModelController.$setViewValue({ year: $scope.year, month: $scope.month,date: $scope.date,hour: $scope.hour,minute: $scope.minute,second: $scope.second });
                });
            },
            templateUrl: '/js/templates/datetime.html'
        }
    });
})();
(function() {
	var app = angular.module('app', ['720kb.datepicker']);

	app.directive('search', ['searchService', 'conversionService', "$rootScope", "$http", "$filter", function(searchService, conversionService, $rootScope, $http, $filter) {
		return {
			restrict: 'E',
            transclude: true,
			link: function($scope, element, attributes, ngModelCtrl) {

                $scope.data = {
                    missions: [],
                    types: []
                };

                $scope.currentSearch = searchService;

                $scope.brokerFilters = {
                    mission:    null,
                    type:       null,
                    before:     null,
                    after:      null,
                    favorited:  null,
                    noted:      null,
                    downloaded: null
                };

                // Update the filters from the search
                $scope.onSearchChange = function() {
                    conversionService.searchesToFilters($scope.brokerFilters, $scope.currentSearch, $scope.data);
                };

                $scope.onFilterUpdate = function(filterType) {
                    conversionService.filtersToSearches($scope.brokerFilters, $scope.currentSearch, filterType);
                };

                $scope.reset = function() {
                    $rootScope.$broadcast('exitSearchMode');
                    $scope.currentSearch.rawQuery = '';
                    $scope.onSearchChange();
                };

                (function() {
                    $http.get('/missioncontrol/search/fetch').then(function(response) {
                        $scope.data = {
                            missions: response.data.missions.map(function(mission) {
                                return {
                                    name: mission.name,
                                    image: mission.featured_image
                                }
                            }),
                            types: response.data.types.map(function(type) {
                                return {
                                    name: type,
                                    image: '/images/icons/' + type.replace(" ", "") + '.jpg'
                                }
                            })
                        }
                    });
                })();
			},
			templateUrl: '/js/templates/search.html'
		}
	}]);

    app.service('conversionService', function() {
        this.searchesToFilters = function(brokerFilters, search, data) {
            // Search for missions in the query string
            var missionResult = search.filters().mission();
            if (missionResult != null) {
                var mission = data.missions.filter(function(mission) {
                    return mission.name.toLowerCase() == missionResult.toLowerCase();
                });

                if (mission !== null) {
                    brokerFilters.mission = mission[0];
                } else {
                    brokerFilters.mission = null;
                }
            } else {
                brokerFilters.mission = null;
            }

            // Search for types of resources in the query string
            var typeResult = search.filters().type();
            if (typeResult != null) {
                var type = data.types.filter(function(type) {
                    return type.name.toLowerCase() == typeResult.toLowerCase();
                });

                if (type !== null) {
                    brokerFilters.type = type[0];
                } else {
                    brokerFilters.type = null;
                }
            } else {
                brokerFilters.type = null;
            }

            var afterResult = search.filters().after();
            if (afterResult != null) {
                brokerFilters.after = moment(afterResult, 'YYYY-MM-DD').format('MMM D, YYYY');
            }  else {
                brokerFilters.after = null;
            }

            var beforeResult = search.filters().before();
            if (beforeResult != null) {
                brokerFilters.before = moment(beforeResult, 'YYYY-MM-DD').format('MMM D, YYYY');
            } else {
                brokerFilters.before = null;
            }

            brokerFilters.favorited = search.filters().favorited() != null;
            brokerFilters.noted = search.filters().noted() != null;
            brokerFilters.downloaded = search.filters().downloaded() != null;

        };

        this.filtersToSearches = function(brokerFilters, search, filterType) {
            if (filterType === 'mission') {
                if (brokerFilters.mission === null) {
                    search.rawQuery = search.rawQuery.replace(search.regex.mission, '');
                } else {
                    if (search.filters().mission() === null) {

                        // Test whether the name of the mission contains a space. If it does, we need to append
                        // quotes around it
                        var whatToConcatenate = /\s/.test(brokerFilters.mission.name) ?
                            'mission:"' + brokerFilters.mission.name + '"' :
                            'mission:' + brokerFilters.mission.name;

                        this.contextualConcat(search, whatToConcatenate);

                    } else {
                        search.rawQuery = /\s/.test(brokerFilters.mission.name) ?
                            search.rawQuery.replace(search.regex.mission, 'mission:"' + brokerFilters.mission.name + '"') :
                            search.rawQuery.replace(search.regex.mission, 'mission:' + brokerFilters.mission.name);
                    }
                }
            }

            else if (filterType == 'type') {
                if (brokerFilters.type === null) {
                    search.rawQuery = search.rawQuery.replace(search.regex.type, '');
                } else {
                    if (search.filters().type() === null) {

                        // Test whether the name of the type contains a space. If it does, we need to append
                        // quotes around it
                        var whatToConcatenate = /\s/.test(brokerFilters.type.name) ?
                        'type:"' + brokerFilters.type.name + '"' :
                        'type:' + brokerFilters.type.name;

                        this.contextualConcat(search, whatToConcatenate);

                    } else {
                        search.rawQuery = /\s/.test(brokerFilters.type.name) ?
                            search.rawQuery.replace(search.regex.type, 'type:"' + brokerFilters.type.name + '"') :
                            search.rawQuery.replace(search.regex.type, 'type:' + brokerFilters.type.name);
                    }
                }
            }

            else if (filterType == 'after') {
                if (brokerFilters.after === null || brokerFilters.after === "") {
                    search.rawQuery = search.rawQuery.replace(search.regex.after, '');
                } else {
                    if (moment(brokerFilters.after, "MMM D, YYYY").isValid()) {
                        var dateToConcatenate = moment(brokerFilters.after, "MMM D, YYYY").format('YYYY-MM-DD');
                        if (search.filters().after() === null) {
                            this.contextualConcat(search, 'after:' + dateToConcatenate);
                        } else {
                            search.rawQuery = search.rawQuery.replace(search.regex.after, 'after:' + dateToConcatenate);
                        }
                    }
                }
            }

            else if (filterType == 'before') {
                if (brokerFilters.before === null || brokerFilters.before === "") {
                    search.rawQuery = search.rawQuery.replace(search.regex.before, '');
                } else {
                    if (moment(brokerFilters.before, "MMM D, YYYY").isValid()) {
                        var dateToConcatenate = moment(brokerFilters.before, "MMM D, YYYY").format('YYYY-MM-DD');
                        if (search.filters().before() === null) {
                            this.contextualConcat(search, 'before:' + dateToConcatenate);
                        } else {
                            search.rawQuery = search.rawQuery.replace(search.regex.before, 'before:' + dateToConcatenate);
                        }
                    }
                }
            }

            else if (filterType === 'favorited') {
                if (search.filters().favorited() === null) {
                    this.contextualConcat(search, 'favorited:true');
                } else {
                    search.rawQuery = search.rawQuery.replace(search.regex.favorited, '');
                }
            }

            else if (filterType === 'noted') {
                if (search.filters().noted() === null) {
                    this.contextualConcat(search, 'noted:true');
                } else {
                    search.rawQuery = search.rawQuery.replace(search.regex.noted, '');
                }
            }

            else if (filterType === 'downloaded') {
                if (search.filters().downloaded() === null) {
                    this.contextualConcat(search, 'downloaded:true');
                } else {
                    search.rawQuery = search.rawQuery.replace(search.regex.downloaded, '');
                }
            }
        };

        this.contextualConcat = function(search, whatToConcatenate) {
            // Add a space so that we can make the search look cleaner (but only if it's not empty and the last character is not a string)
            if (search.rawQuery != "" && search.rawQuery.slice(-1) != ' ') {
                whatToConcatenate = ' ' + whatToConcatenate;
            }
            search.rawQuery = search.rawQuery.concat(whatToConcatenate);
        };

        this.contextualRemove = function(search, whatToRemove) {

        };
    });

    /**
     *  Eventually we could cache the outputs of the searchTerm and filters to make sure we don't re-regex
     *  things we don't have to?
     */
    app.service('searchService', function() {
        var self = this;

        self.rawQuery = "";

        self.searchTerm = function () {
            return self.rawQuery.replace(self.regex.tags, "").replace(self.regex.all, "").trim();
        };

        // https://regex101.com/r/uL9jN5/1
        // https://regex101.com/r/iT2zH5/2
        self.filters = function() {
            return {
                tags: function () {
                    var match;
                    var tags = [];

                    while (match = self.regex.tags.exec(self.rawQuery)) {
                        tags.push(match[1]);
                    }
                    return tags;
                },
                mission: function () {
                    var missionResult = self.regex.mission.exec(self.rawQuery);
                    return missionResult !== null ? (!angular.isUndefined(missionResult[1]) ? missionResult[1] : missionResult[2]) : null;
                },
                type: function () {
                    var typeResult = self.regex.type.exec(self.rawQuery);
                    return typeResult !== null ? (!angular.isUndefined(typeResult[1]) ? typeResult[1] : typeResult[2]) : null;
                },
                before: function () {
                    var beforeResult = self.regex.before.exec(self.rawQuery);
                    return beforeResult !== null ? beforeResult[1] : null;
                },
                after: function () {
                    var afterResult = self.regex.after.exec(self.rawQuery);
                    return afterResult !== null ? afterResult[1] : null;
                },
                year: function () {
                    var yearResult = self.regex.year.exec(self.rawQuery);
                    return yearResult !== null ? yearResult[1] : null;
                },
                user: function () {
                    var userResult = self.regex.user.exec(self.rawQuery);
                    return userResult !== null ? userResult[1] : null;
                },
                favorited: function () {
                    var favoritedResult = self.regex.favorited.exec(self.rawQuery);
                    return favoritedResult !== null ? favoritedResult[0] : null;
                },
                noted: function () {
                    var notedResult = self.regex.noted.exec(self.rawQuery);
                    return notedResult !== null ? notedResult[0] : null;
                },
                downloaded: function () {
                    var downloadedResult = self.regex.downloaded.exec(self.rawQuery);
                    return downloadedResult !== null ? downloadedResult[0] : null;
                }
            }
        };

        self.toQuery = function() {
            return {
                searchTerm: self.searchTerm(),
                filters: {
                    tags: self.filters().tags(),
                    mission: self.filters().mission(),
                    type: self.filters().type(),
                    before: self.filters().before(),
                    after: self.filters().after(),
                    year: self.filters().year(),
                    user: self.filters().user(),
                    favorited: self.filters().favorited(),
                    noted: self.filters().noted(),
                    downloaded: self.filters().downloaded()
                }
            }
        };

        self.regex = {
            tags: /\[([^)]+?)\]/gi,
            mission: /mission:(?:([^ "]+)|"(.+)")/i,
            type: /type:(?:([^ "]+)|"(.+)")/i,
            before: /before:([0-9]{4}-[0-9]{2}-[0-9]{2})/i,
            after:/after:([0-9]{4}-[0-9]{2}-[0-9]{2})/i,
            year: /year:([0-9]{4})/i,
            user: /uploaded-by:([a-zA-Z0-9_-]+)/i,
            favorited: /favorited:(true|yes|y|1)/i,
            noted: /noted:(true|yes|y|1)/i,
            downloaded: /downloaded:(true|yes|y|1)/i,
            all: /([a-z-]+):(?:([^ "]+)|"(.+)")/gi
        };

        return self;
    });
})();
//http://codepen.io/jakob-e/pen/eNBQaP
(function() {
    var app = angular.module('app');

    app.directive('passwordToggle', ["$compile", function($compile) {
        return {
            restrict: 'A',
            scope:{},
            link: function(scope, elem, attrs){
                scope.tgl = function() {
                    elem.attr('type',(elem.attr('type')==='text'?'password':'text'));
                };
                var lnk = angular.element('<i class="fa fa-eye" data-ng-click="tgl()"></i>');
                $compile(lnk)(scope);
                elem.wrap('<div class="password-toggle"/>').after(lnk);
            }
        }
    }]);
})();

(function() {
    var app = angular.module('app');

    app.directive('chart', ["$window", function($window) {
        return {
            replace: true,
            restrict: 'E',
            scope: {
                data: '=data',
                settings: "=",
                type: '@'
            },
            link: function($scope, elem, attrs) {

                $scope.$watch('data', function(newValue) {
                    render(newValue);
                }, true);

                $scope.$on('chart:rerender', function() {
                    elem.empty();
                    render($scope.data);
                });

                function render(chartData) {
                    if (!angular.isDefined(chartData) || chartData.length == 0) {
                        return;
                    }

                    // Make a deep copy of the object as we may be doing manipulation
                    // which would cause the watcher to fire
                    var data = jQuery.extend(true, [], chartData);

                    var d3 = $window.d3;
                    var svg = d3.select(elem[0]);
                    var width = elem.width();
                    var height = elem.height();

                    // Settings for rendering the chart
                    var settings = $scope.settings;

                    // Core information used to render the chart
                    var core = {};

                    // create a reasonable set of defaults for some things
                    if (typeof settings.xAxis.ticks === 'undefined') {
                        settings.xAxis.ticks = 5;
                    }
                    if (typeof settings.yAxis.ticks === 'undefined') {
                        settings.yAxis.ticks = 5;
                    }

                    // check padding and set default
                    if (typeof settings.padding === 'undefined') {
                        settings.padding = 50;
                    }

                    // draw
                    if ($scope.type == 'bar') {
                        drawBarChart();
                    } else {
                        drawLineChart();
                    }

                    function drawChart() {
                        // Element manipulation
                        var xAxisLine = svg.append("svg:g")
                            .attr("class", "x axis")
                            .attr("transform", "translate(0," + (height - settings.padding) + ")")
                            .call(core.xAxisGenerator);

                        var yAxisLine = svg.append("svg:g")
                            .attr("class", "y axis")
                            .attr("transform", "translate(" + settings.padding + ",0)")
                            .attr("stroke-width", 2)
                            .call(core.yAxisGenerator);

                        svg.append("text")
                            .attr("class", "chart-title")
                            .attr("text-anchor", "middle")
                            .attr("x", width / 2)
                            .attr("y", settings.padding / 2)
                            .text(settings.chartTitle);

                        svg.append("text")
                            .attr("class", "axis x-axis")
                            .attr("text-anchor", "middle")
                            .attr("x", width / 2)
                            .attr("y", height)
                            .text(settings.xAxis.title);

                        svg.append("text")
                            .attr("class", "axis y-axis")
                            .attr("text-anchor", "middle")
                            .attr("transform", "rotate(-90)")
                            .attr("x", - (height / 2))
                            .attr("y", settings.padding / 2)
                            .text(settings.yAxis.title);
                    }

                    function computeChart() {
                        // Setup xZeroing
                        var startPoint = settings.xAxis.zeroing ? 0 : data[0][settings.xAxis.key];

                        // Setup xScales
                        if (settings.xAxis.type == 'linear') {
                            core.xScale = d3.scale.linear()
                                .domain([startPoint, data[data.length-1][settings.xAxis.key]])
                                .range([settings.padding, width - settings.padding]);

                        } else if (settings.xAxis.type == 'timescale') {
                            core.xScale = d3.time.scale.utc()
                                .domain([startPoint, data[data.length-1][settings.xAxis.key]])
                                .range([settings.padding, width - settings.padding]);

                        } else if (settings.xAxis.type == 'ordinal') {

                            core.xScale = d3.scale.ordinal()
                                .domain(data.map(function(dataBit) { return dataBit[settings.xAxis.key]; }))
                                .rangeRoundBands([settings.padding, width - settings.padding], 0.5);
                        }

                        // setup yZeroing
                        var startPoint = settings.yAxis.zeroing ? 0 : data[0][settings.yAxis.key];

                        // setup yScales
                        if (settings.yAxis.type == 'linear') {
                            core.yScale = d3.scale.linear()
                                .domain([d3.max(data, function(d) {
                                    if (typeof d[settings.yAxis.key] === 'string') {
                                        return parseFloat(d[settings.yAxis.key]);
                                    }
                                    return d[settings.yAxis.key];
                                }), startPoint])
                                .range([settings.padding, height - settings.padding]);

                        } else if (settings.yAxis.type == 'timescale') {
                            core.yScale = d3.time.scale.utc()
                                .domain([d3.max(data, function(d) {
                                    return d[settings.yAxis.key];
                                }), startPoint])
                                .range([settings.padding, height - settings.padding]);
                        }

                        // Generators
                        core.xAxisGenerator = d3.svg.axis().scale(core.xScale).orient('bottom').ticks(settings.xAxis.ticks).tickFormat(function(d) {
                            return typeof settings.xAxis.formatter !== 'undefined' ? settings.xAxis.formatter(d) : d;
                        });

                        core.yAxisGenerator = d3.svg.axis().scale(core.yScale).orient("left").ticks(settings.yAxis.ticks).tickFormat(function(d) {
                            return typeof settings.yAxis.formatter !== 'undefined' ? settings.yAxis.formatter(d) : d;
                        });
                    };

                    function drawBarChart() {

                        computeChart();

                        svg.selectAll("bar")
                            .data(data)
                            .enter().append("rect")
                            .style("fill", "steelblue")
                            .attr("x", function(d) {
                                return core.xScale(d[settings.xAxis.key]);
                            })
                            .attr("width", core.xScale.rangeBand())
                            .attr("y", function(d) {
                                return core.yScale(d[settings.yAxis.key]);
                            })
                            .attr("height", function(d) {
                                return height - core.yScale(d[settings.yAxis.key]) - settings.padding;
                            });

                        drawChart();
                    };

                    function drawLineChart() {

                        computeChart();

                        // Line function
                        var lineFunction = d3.svg.line()
                            .x(function(d) {
                                return core.xScale(d[settings.xAxis.key]);
                            })
                            .y(function(d) {
                                return core.yScale(d[settings.yAxis.key]);
                            })
                            .interpolate(settings.interpolation);

                        svg.append("svg:path")
                            .attr({
                                d: lineFunction(data),
                                "stroke-width": 2,
                                "fill": "none",
                                "class": "path"
                            });

                        drawChart();
                    };
                }

            },
            templateUrl: '/js/templates/chart.html'
        }
    }]);
})();
(function() {
    var app = angular.module('app');

    app.directive('redditComment', ["$http", function($http) {
        return {
            replace: true,
            restrict: 'E',
            scope: {
                redditComment: '=ngModel'
            },
            link: function($scope, element, attributes) {

                $scope.retrieveRedditComment = function() {
                    if (typeof $scope.redditComment.external_url !== "undefined") {
                        $http.get('/missioncontrol/create/retrieveredditcomment?url=' + encodeURIComponent($scope.redditComment.external_url)).then(function(response) {

                            // Set properties on object
                            $scope.redditComment.summary = response.data.data.body;
                            $scope.redditComment.author = response.data.data.author;
                            $scope.redditComment.reddit_comment_id = response.data.data.name;
                            $scope.redditComment.reddit_parent_id = response.data.data.parent_id; // make sure to check if the parent is a comment or not
                            $scope.redditComment.reddit_subreddit = response.data.data.subreddit;
                            $scope.redditComment.originated_at = moment.unix(response.data.data.created_utc).format();
                        });
                    }
                }

            },
            templateUrl: '/js/templates/redditComment.html'
        }
    }]);
})();
(function() {
    var app = angular.module('app', []);

    app.directive("dropdown", function() {
        return {
            restrict: 'E',
            require: '^ngModel',
            scope: {
                data: '=options',
                uniqueKey: '@',
                titleKey: '@',
                imageKey: '@?',
                descriptionKey: '@?',
                searchable: '@',
                placeholder: '@',
                idOnly: '@?'
            },
            link: function($scope, element, attributes, ngModelCtrl) {

                $scope.search = {
                    name: ''
                };

                $scope.thumbnails = angular.isDefined($scope.imageKey);

                ngModelCtrl.$viewChangeListeners.push(function() {
                    $scope.$eval(attributes.ngChange);
                });

                $scope.mapData = function() {
                    if (!angular.isDefined($scope.data)) {
                        return;
                    }

                    return $scope.data.map(function(option) {
                        var props = {
                            id: option[$scope.uniqueKey],
                            name: option[$scope.titleKey],
                            image: option[$scope.imageKey]
                        };

                        if (typeof $scope.descriptionKey !== 'undefined') {
                            props.description = option[$scope.descriptionKey];
                        }

                        return props;
                    });
                };

                $scope.options = $scope.mapData();

                $scope.$watch("data", function() {
                    $scope.options = $scope.mapData();
                    ngModelCtrl.$setViewValue(ngModelCtrl.$viewValue);
                });

                ngModelCtrl.$render = function() {
                    $scope.selectedOption = ngModelCtrl.$viewValue;
                };

                ngModelCtrl.$parsers.push(function(viewValue) {
                    if ($scope.idOnly === 'true') {
                        return viewValue.id;
                    } else {
                        return viewValue;
                    }
                });

                ngModelCtrl.$formatters.push(function(modelValue) {
                        if ($scope.idOnly === 'true' && angular.isDefined($scope.options)) {
                            return $scope.options.filter(function(option) {
                                return option.id = modelValue;
                            }).shift();
                        } else {
                            return modelValue;
                        }
                });

                $scope.selectOption = function(option) {
                    $scope.selectedOption = option;
                    ngModelCtrl.$setViewValue(option);
                    $scope.dropdownIsVisible = false;
                };

                $scope.toggleDropdown = function() {
                    $scope.dropdownIsVisible = !$scope.dropdownIsVisible;
                    if (!$scope.dropdownIsVisible) {
                        $scope.search.name = '';
                    }
                };

                $scope.dropdownIsVisible = false;
            },
            templateUrl: '/js/templates/dropdown.html'
        }
    });
})();

(function() {
    var app = angular.module('app');

    app.directive('uniqueUsername', ["$q", "$http", function($q, $http) {
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function(scope, elem, attrs, ngModelCtrl) {
                ngModelCtrl.$asyncValidators.username = function(modelValue, viewValue) {
                    return $http.get('/auth/isusernametaken/' + modelValue).then(function(response) {
                        return response.data.taken ? $q.reject() : true;
                    });
                };
            }
        }
    }]);
})();

(function() {
    var app = angular.module('app');

    app.directive('characterCounter', ["$compile", function($compile) {
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function($scope, element, attributes, ngModelCtrl) {
                var counter = angular.element('<p class="character-counter" ng-class="{ red: isInvalid }">{{ characterCounterStatement }}</p>');
                $compile(counter)($scope);
                element.after(counter);

                ngModelCtrl.$parsers.push(function(viewValue) {
                    $scope.isInvalid = ngModelCtrl.$invalid;
                    if (attributes.ngMinlength > ngModelCtrl.$viewValue.length) {
                        $scope.characterCounterStatement = attributes.ngMinlength - ngModelCtrl.$viewValue.length + ' to go';
                    } else if (attributes.ngMinlength <= ngModelCtrl.$viewValue.length) {
                        $scope.characterCounterStatement = ngModelCtrl.$viewValue.length + ' characters';
                    }
                    return viewValue;
                });
            }
        }
    }]);
})();
(function() {
    var app = angular.module('app', []);

    app.directive('timeline', ["missionDataService", function(missionDataService) {
        return {
            restrict: 'E',
            scope: {
                mission: '='
            },
            link: function(scope, element, attributes) {
                missionDataService.launchEvents(scope.mission.slug).then(function(response) {

                    var timespans = {
                        ONE_YEAR: 365 * 86400,
                        SIX_MONTHS: 6 * 30 * 86400,
                        ONE_MONTH: 30 * 86400
                    };

                    scope.launchEvents = response.data.map(function(launchEvent) {
                        launchEvent.occurred_at = moment.utc(launchEvent.occurred_at);
                        return launchEvent;
                    });

                    if (scope.mission.status == 'Complete') {
                        scope.launchEvents.push({
                            'event': 'Launch',
                            'occurred_at': moment.utc(scope.mission.launch_date_time)
                        });
                    }

                    // Add 10% to the minimum and maximum dates
                    var timespan = Math.abs(scope.launchEvents[0].occurred_at.diff(scope.launchEvents[scope.launchEvents.length-1].occurred_at, 'seconds'));
                    var dates = {
                        min: moment(scope.launchEvents[0].occurred_at).subtract(timespan / 10, 'seconds').toDate(),
                        max: moment(scope.launchEvents[scope.launchEvents.length-1].occurred_at).add(timespan / 10, 'seconds').toDate()
                    };

                    var elem = $(element).find('svg');

                    var svg = d3.select(elem[0]).data(scope.launchEvents);

                    var xScale = d3.time.scale.utc()
                        .domain([dates.min, dates.max])
                        .range([0, $(elem[0]).width()]);

                    // Determine ticks to use
                    if (timespan > timespans.ONE_YEAR) {
                        var preferredTick = {
                            frequency: d3.time.month,
                            format: d3.time.format("%b %Y")
                        };
                    } else if (timespan > timespans.SIX_MONTHS) {
                        var preferredTick = {
                            frequency: d3.time.month,
                            format: d3.time.format("%b %Y")
                        };
                    } else if (timespan > timespans.ONE_MONTH) {
                        var preferredTick = {
                            frequency: d3.time.week,
                            format: d3.time.format("%e %b")
                        };
                    } else {
                        var preferredTick = {
                            frequency: d3.time.day,
                            format: d3.time.format("%e %b")
                        };
                    }

                    var xAxisGenerator = d3.svg.axis().scale(xScale).orient('bottom')
                        .ticks(preferredTick.frequency, 1)
                        .tickFormat(preferredTick.format)
                        .tickPadding(25);

                    var axis = svg.append("svg:g")
                        .attr("class", "x axis")
                        .attr("transform", "translate(0," + 3 * $(elem[0]).height() / 4 + ")")
                        .call(xAxisGenerator);

                    var tip = d3.tip().attr('class', 'tip').html(function(d) {
                        return d.event;
                    }).offset([0, -20]);

                    var g = svg.append("g")
                        .attr("transform", "translate(0," + 3 * $(elem[0]).height() / 4 + ")")
                        .selectAll("circle")
                        .data(scope.launchEvents.map(function(launchEvent) {
                            launchEvent.occurred_at.toDate();
                            return launchEvent;
                        }))
                        .enter().append("circle")
                        .attr("r", 20)
                        .attr('class', function(d) {
                            return d.event.toLowerCase().replace(/\s/g, "-");
                        })
                        .classed('event', true)
                        .attr("cx", function(d) { return xScale(d.occurred_at); })
                        .call(tip)
                        .on("mouseover", function(d) {

                            d3.selectAll('.event').transition()
                                .attr('opacity', 0);

                            d3.select(this).transition()
                                .attr('opacity', 1)
                                .attr("transform", "translate(-"+ d3.select(this).attr('cx') * (1.5-1) + ",-0) scale(1.5, 1.5)");
                            tip.show(d);
                        })
                        .on("mouseout", function(d) {

                            d3.selectAll('.event').transition()
                                .attr("transform", "translate(0,0) scale(1,1)")
                                .attr('opacity', 1);
                            tip.hide(d);
                        });

                    // replace tick lines with circles
                    var ticks = axis.selectAll(".tick");
                    ticks.each(function() { d3.select(this).append("circle").attr("r", 3); });
                    ticks.selectAll("line").remove();
                });
            },
            templateUrl: '/js/templates/timeline.html'
        };
    }]);
})();
(function() {
    var app = angular.module('app');

    app.directive('launchDateValidity', [function() {
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function($scope, element, attributes, ngModelCtrl) {

                var subs = ['Early', 'Mid', 'Late'].join('|');
                var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October',
                'November', 'December'].join('|');
                var quarters = ['Q1', 'Q2', 'Q3', 'Q4'].join('|');
                var halfs = ['H1', 'H2'].join('|');


                ngModelCtrl.$validators.launchDateValidity = function(value) {
                    if (angular.isDefined(value) && value !== null) {
                        // Check day and date specificities
                        if (moment(value, 'YYYY-MM-DD H:mm:ss', true).isValid()) {
                            return true;
                        }

                        // Submonth specificities
                        var submonthRegex = new RegExp('^(' + subs + ') (' + months + ') \\d{4}$');
                        // Month specificities
                        var monthRegex = new RegExp('^(' + months + ') \\d{4}$');
                        // Quarter specifities
                        var quarterRegex = new RegExp('^(' + quarters + ') \\d{4}$');
                        // Subyear specificities
                        var subyearRegex = new RegExp('^(' + subs + ') \\d{4}$');
                        // Half specificities
                        var halfRegex = new RegExp('^(' + halfs + ') \\d{4}$');
                        // Year specificities
                        var yearRegex = new RegExp('^\\d{4}$');

                        var regexes = [submonthRegex, monthRegex, quarterRegex, subyearRegex, halfRegex, yearRegex];

                        regexes.every(function(regex) {
                            if (regex.test(value)) {
                                return true;
                            }
                        });
                    }
                    return false;
                };
            }
        }
    }]);
})();
(function() {
    var app = angular.module('app');

    app.directive('objectCard', function() {
        return {
            restrict: 'E',
            replace: true,
            scope: {
                object: '='
            },
            link: function($scope) {
            },
            templateUrl: '/js/templates/objectCard.html'
        }
    });
})();
(function() {
    var app = angular.module('app');

    app.filter('jsonPrettify', function() {
       return function(input) {
           if (typeof input !== 'undefined') {
               return JSON.stringify(input, null, 2);
           }
           return null;
       }
    });
})();