console.log("customQuestionSet: ", H5P);
var Howl;

(function ($) {
    $(document).ready(function () {
        var soundQueue = [];
        var audioElement = document.createElement("audio");
        var pageTimeout;
        var playTimeout;

        function createSound(soundQueue){
            if (soundQueue && soundQueue.length) {
                soundQueue = soundQueue.map(function(text) {
                    return text.replace(/&nbsp;/g, '').replace('A:', '').replace('B:', '').replace(/<[^>]+>/g, '').trim();
                });
            }
            $.ajax({
                type: "POST",
                url: "https://tts-dev.esl4u.net/api/tts/multiple-text",
                data: JSON.stringify({"texts": soundQueue}),
                contentType: "application/json; charset=utf-8",
                crossDomain: true,
                dataType: "json",
                success: function (data, status, jqXHR) {
                },
                error: function (jqXHR, status) {
                    console.log(jqXHR);
                }
            });
        }

        audioElement.onended = function() {
            console.log("The audio has ended");
            playTimeout = setTimeout(function(){
                if (soundQueue && soundQueue.length) {
                    _playSound();
                }
            }, 1000);
        };

        var isValidHttpUrl = function (string) {
            var url;
            try {
                url = new URL(string);
            } catch (_) {
                return false;
            }
            return url.protocol === "http:" || url.protocol === "https:";
        };

        var _playSound = function() {
            if (soundQueue && soundQueue.length) {
                var currentSoundName = soundQueue.shift();
                if (currentSoundName && currentSoundName.length) {
                    var source = isValidHttpUrl(currentSoundName) ? currentSoundName : "https://tts-dev.esl4u.net/sound/" + currentSoundName + ".wav"
                    console.log('_play: ', source);
                    var sound = new Howl({
                        src: [source]
                    });
                    sound.once('load', function(){
                        sound.play();
                    });
                }
            }
        }

        function slugify(text) {
            if (text && text.length) {
                var plainText = text.replace(/&nbsp;/g, '').replace('A:', '').replace('B:', '').replace(/<[^>]+>/g, '').trim();
                return plainText.toLowerCase()
                    .replace(/ /g,'-')
                    .replace(/[^\w-]+/g,'');
            }
            return '';
        }

        var playMultipleSounds = function(actionTextList, soundNames) {
            soundQueue = soundNames.filter(function(name){
                return name && name.length;
            });
            clearTimeout(playTimeout);
            _playSound();
            if (actionTextList && actionTextList.length) {
                actionTextList = actionTextList.filter(function(name){
                    return name && name.length;
                });
                createSound(actionTextList);
            }
        }

        var playSoundFromSlide = function (slide) {
            var slideElements = slide.elements;
            console.log('slideElements: ', slideElements);

            const audioElements = slideElements.filter(function(element){
                return element.action.metadata.contentType === "Audio";
            });
            console.log('audioElements: ', audioElements);
            const textElements = slideElements.filter(function(element){
                return element.action.metadata.contentType === "Text";
            });
            console.log('textElements: ', textElements);
            const imageElements = slideElements.filter(function(element){
                return element.action.metadata.contentType === "Image";
            });
            console.log('imageElements: ', imageElements);

            if (audioElements && audioElements.length) {
                const audioSourceList = [];
                audioElements.forEach(function(element){
                    console.log('audio element: ', element);
                    const params = element.action.params;
                    console.log('audio params: ', params);
                    const files = params.files;
                    console.log('audio files: ', files);
                    files.forEach(function(file) {
                        audioSourceList.push(file.path);
                    });
                });
                console.log('audioSourceList: ', audioSourceList);
                playMultipleSounds(null, audioSourceList);
            } else {
                const soundElements = (textElements.length) ? textElements : imageElements;
                console.log('soundElements: ', soundElements);
                const actionTextList = soundElements.map(function(element){
                    console.log('soundElements element: ', element);
                    const params = element.action.params;
                    const alText = params.alt || params.text;
                    console.log('soundElements alText: ', alText);
                    return alText;
                });
                const slugifyTextList = actionTextList.map(function(element){
                    return slugify(element);
                });
                console.log('actionTextList: ', actionTextList);
                playMultipleSounds(actionTextList, slugifyTextList);
            }
        }

        var h5pInstances = H5P.instances;
        var h5pInstance = h5pInstances[0];

        $.getScript("https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.3/howler.js", function() {
            console.log("howler.min.js loaded: ", Howl);
        });

        H5P.externalDispatcher.on('xAPI', function (event) {
            console.log('H5P.externalDispatcher: ', event);
            var verb = event.data.statement.verb.id;
            console.log('verb: ', verb);
            if (verb === "http://adlnet.gov/expapi/verbs/answered") {
                var result = event.data.statement.result;
                console.log('result: ', result);
                if (result) {
                    var completion = result.completion;
                    var maxScore = result.score.max;
                    var rawScore = result.score.raw;
                    if (completion && (rawScore >= maxScore)) {
                        var object = event.data.statement.object;
                        var definition = object.definition;
                        var choices = definition.choices;
                        var correctResponsesPattern = definition.correctResponsesPattern;
                        var correctChoices = choices.filter(function(choice) {
                            return correctResponsesPattern.includes(choice.id);
                        });
                        var actionTextList = correctChoices.map(function(choice) {
                            var altText = choice.description['en-US'];
                            return altText;
                        });
                        var alTexts = actionTextList.map(function(altText) {
                            return slugify(altText);
                        });

                        playMultipleSounds(actionTextList, alTexts);
                    }
                }
            }
        });
    });
})(H5P.jQuery);
