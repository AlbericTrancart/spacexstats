(function() {
    var app = angular.module('app', []);

    app.controller("uploadAppController", ["$scope", function($scope) {
        $scope.activeSection = "upload";

        $scope.data = {
            missions: laravel.missions,
            tags: laravel.tags,
            subtypes: {
                images: [
                    {value: 1, display: 'Mission Patch' },
                    {value: 2, display: 'Photo' },
                    {value: 4, display: 'Chart' },
                    {value: 5, display: 'Screenshot' },
                    {value: 10, display: 'Infographic' },
                    {value: 11, display: 'News Summary' },
                    {value: 16, display: 'Hazard Map' },
                    {value: 17, display: 'License' },
                ],
                video: [
                    {value: 6, display: 'Launch Video' },
                    {value: 7, display: 'Press Conference' }
                ],
                documents: [
                    {value: 6, display: 'Press Kit' },
                    {value: 7, display: 'Cargo Manifest' },
                    {value: 15, display: 'Weather Forecast' },
                    {value: 17, display: 'License' }
                ]
            }
        };

        $scope.changeSection = function(section) {
            $scope.activeSection = section;
        }
    }]);

    app.controller("uploadController", ["$rootScope", "$scope", "objectFromFile", function($rootScope, $scope, objectFromFile) {
        $scope.activeUploadSection = "dropzone";

        $scope.currentVisibleFile = null;
        $scope.isVisibleFile = function(file) {
            return $scope.currentVisibleFile === file;
        };
        $scope.setVisibleFile = function(file) {
            $scope.currentVisibleFile = file;
        };

        $scope.uploadCallback = function() {

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
            $scope.$apply();
        };

        $scope.fileSubmitButtonFunction = function() {
            $rootScope.postToMissionControl($scope.files, 'files');
        }
    }]);

    app.controller("postController", ["$rootScope", "$scope", "$http", function($rootScope, $scope, $http) {

        $scope.NSFcomment = {};
        $scope.redditcomment = {};
        $scope.pressrelease = {};

        $scope.postSubmitButtonFunction = function() {
            switch ($scope.postType) {
                case 'NSFcomment' :     $rootScope.postToMissionControl($scope.NSFcomment, 'NSFcomment'); break;
                case 'redditcomment' :  $rootScope.postToMissionControl($scope.redditcomment, 'redditcomment'); break;
                case 'pressrelease' :  $rootScope.postToMissionControl($scope.pressrelease, 'pressrelease'); break;
            }
        }
    }]);

    app.controller("writeController", ["$rootScope", "$scope", function($rootScope, $scope) {

        $scope.text = {
            title: null,
            content: null,
            mission_id: null,
            anonymous: null,
            tags: []
        };

        $scope.writeSubmitButtonFunction = function() {
            $rootScope.postToMissionControl($scope.text, 'text');
        }
    }]);

    app.run(['$rootScope', '$http', function($rootScope, $http) {
        $rootScope.postToMissionControl = function(dataToUpload, submissionHeader) {
            var req = {
                method: 'POST',
                url: '/missioncontrol/create/submit',
                headers: {
                    'Submission-Type': submissionHeader
                },
                data: {
                    data: dataToUpload
                }
            };

            $http(req).then(function() {
                window.location = '/missioncontrol';
            });
        }
    }]);

    app.factory("Image", function() {
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

            self.datetimeExtractedFromEXIF = self.originated_at !== null ? true : false;

            return self;
        }
    });

    app.factory("GIF", function() {
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

    app.factory("Audio", function() {
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

    app.factory("Video", function() {
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

    app.factory("Document", function() {
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

    app.service("objectFromFile", ["Image", "GIF", "Audio", "Video", "Document", function(Image, GIF, Audio, Video, Document) {
        this.create = function(file, index) {
            switch(file.type) {
                case 1: return new Image(file, index);
                case 2: return new GIF(file, index);
                case 3: return new Audio(file, index);
                case 4: return new Video(file, index);
                case 5: return new Document(file, index);
                default: return null;
            }
        }
    }]);
})();