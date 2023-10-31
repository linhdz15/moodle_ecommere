console.log("customPresentation MIN_MATCH_PERCENT 0.7: ", H5P);
var annyang;
var Howl;
var stringSimilarity;
var MIN_MATCH_PERCENT = 0.7;
var MICROPHONE_ICON_URL = "https://cdn.esl4u.net/icon/listening-microphone.gif";
var CORRECT_ICON_URL = "https://cdn.esl4u.net/icon/microphone-correct.png";
var INCORRECT_ICON_URL = "https://cdn.esl4u.net/icon/microphone-incorrect.png";

var CORRECT_SOUND_URL = "https://cdn.esl4u.net/sound/sys_correct.wav";
var INCORRECT_SOUND_URL = "https://cdn.esl4u.net/sound/sys_incorrect.wav";

(function ($) {
    $(document).ready(function () {

        var soundQueue = [];
        var soundSpeakQueue = [];
        var globalSound;
        var pageTimeout;
        var playTimeout;
        var initTimeout;
        var correctTexts;
        var listeningText;
        var listeningElement;
        var microphoneElements;
        var speakingErrorCount = 0;
        // var audioElement = document.createElement("audio");
        // audioElement.setAttribute('preload', 'metadata');
        // var audioElement = new Audio();
        // audioElement.setAttribute('muted', 'false');
        // audioElement.autoplay = true;
        // audioElement.onended = _onSoundEnded;

        // var notifyAudioElement = document.createElement("audio");
        // var notifyAudioElement = new Audio();
        // notifyAudioElement.setAttribute('muted', 'false');
        // notifyAudioElement.autoplay = true;
        // document.body.appendChild(audioElement);

        var _playNotifySound = function(source, onNotifyEnd) {
            console.log('playNotifySound: ', source);
            var sound = new Howl({
                src: [source]
            });
            sound.play();
            if (onNotifyEnd) {
                sound.once('end', onNotifyEnd);
            }
        }

        function removeHtml(text) {
            if (text && text.length)
                return text.replace(/&#(\d+);/g, function(match, dec) {
                    return String.fromCharCode(dec);
                }).replace(/&nbsp;/g, '')
                    .replace('A:', '')
                    .replace('B:', '')
                    .replace(/<[^>]+>/g, '').trim();
            return '';
        }

        function createSound(soundQueue){
            if (soundQueue && soundQueue.length) {
                soundQueue = soundQueue.map(function(text) {
                    return removeHtml(text);
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

        var _onSoundEnded = function() {
            console.log("The audio has ended");
            playTimeout = setTimeout(function(){
                if (soundQueue && soundQueue.length) {
                    _playSound();
                }
                if (soundSpeakQueue && soundSpeakQueue.length) {
                    _playSpeakingSound();
                }
            }, 500);
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
                    _play(currentSoundName);
                }
            }
        }

        var _play = function(soundName) {
            var source = isValidHttpUrl(soundName) ? soundName : "https://cdn.esl4u.net/sound/" + soundName + ".wav";
            console.log('_play: ', source);
            if (globalSound && globalSound.stop) {
                globalSound.stop();
            }
            globalSound = new Howl({
                src: [source]
            });
            globalSound.play();
            globalSound.on('end', _onSoundEnded);
        }

        function slugify(text) {
            if (text && text.length) {
                var plainText = text.replace(/&#(\d+);/g, function(match, dec) {
                    return String.fromCharCode(dec);
                }).replace(/&nbsp;/g, '').replace('A:', '').replace('B:', '').replace(/<[^>]+>/g, '').trim();
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
                audioElements.sort(function(a, b){
                    if (a.y === b.y) {
                        return a.x - b.x;
                    }
                    return a.y - b.y;
                });
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
                soundElements.sort(function(a, b){
                    if (a.y === b.y) {
                        return a.x - b.x;
                    }
                    return a.y - b.y;
                });
                console.log('soundElements: ', soundElements);
                const actionTextList = soundElements.map(function(element){
                    console.log('soundElements element: ', element);
                    const params = element.action.params;
                    const alText = params.alt || params.text;
                    console.log('soundElements alText: ', alText);
                    return alText;
                });
                const slugifyTextList = actionTextList.map(function(alText){
                    return slugify(alText);
                });
                playMultipleSounds(actionTextList, slugifyTextList);
            }
        }

        var processAnsweredSlide = function(event) {
            if (event.data.statement.verb.id === "http://adlnet.gov/expapi/verbs/answered") {
                var result = event.data.statement.result;
                console.log('result: ', result);
                if (result) {
                    var completion = result.completion;
                    // var success = result.success;
                    var maxScore = result.score.max;
                    var rawScore = result.score.raw;
                    if (completion && (rawScore >= maxScore) && correctTexts && correctTexts.length) {
                        console.log('correctTexts: ', correctTexts);
                        var sounds = correctTexts.map(function(text){
                            return slugify(text);
                        });
                        playMultipleSounds(correctTexts, sounds);
                    }
                }
            }
        }

        var processMultichoiceSlide = function(multiChoiceElements) {
            var multiChoice = multiChoiceElements[0];
            var answers = multiChoice.action.params.answers;
            var correctAnswers = answers.filter(function(answer){
                return answer.correct;
            });
            correctTexts = [correctAnswers[0].text];
        }

        var processFillBlankSlide = function(fillBlankElements) {
            var fillBlankElement = fillBlankElements[0];
            console.log('fillBlankElement: ', fillBlankElement);
            var questions = fillBlankElement.action.params.questions;
            correctTexts = questions;
        }

        function endListening(userSaidList) {
            // annyang.pause();
            annyang.abort();
            var isSuccess = false;
            console.log('listeningText: ', listeningText);
            console.log('queryText: ', userSaidList);
            if (userSaidList && userSaidList.length) {
                userSaidList = userSaidList.map(function(text){
                    return text.toLowerCase();
                });
                for (var i=0; i<userSaidList.length; i++) {
                    var matchPercent = stringSimilarity.compareTwoStrings(userSaidList[i], listeningText);
                    console.log('matchPercent: ', matchPercent);
                    if (matchPercent >= MIN_MATCH_PERCENT) {
                        isSuccess = true;
                        break;
                    }
                }
            }
            console.log('isSuccess: ', isSuccess);
            if (isSuccess === true) {
                speakingErrorCount = 0;
                listeningElement.htmlElement.src = CORRECT_ICON_URL;
                _playNotifySound(CORRECT_SOUND_URL, function() {
                    listeningElement.htmlElement.style.display = "none";
                    _playSpeakingSound();
                });
            } else {
                speakingErrorCount += 1;
                listeningElement.htmlElement.src = INCORRECT_ICON_URL;
                _playNotifySound(INCORRECT_SOUND_URL, function() {
                    setTimeout(function(){
                        listeningElement.htmlElement.src = MICROPHONE_ICON_URL;
                        annyang.resume();
                    }, 200);
                });
            }
            if(speakingErrorCount > 2) {
                _visibleNavigator(true);
            }
        }

        function onCommandTrigger(params) {
            console.log('onCommandTrigger: ', params);
            speakingErrorCount = 0;
            listeningElement.htmlElement.src = CORRECT_ICON_URL;
            _playNotifySound(CORRECT_SOUND_URL, function() {
                listeningElement.htmlElement.style.display = "none";
                _playSpeakingSound();
            });
        }

        function startListening(microphoneElement, text) {
            speakingErrorCount = 0;
            console.log("startListening: ", text);
            listeningText = text.replace(/&#(\d+);/g, function(match, dec) {
                return String.fromCharCode(dec);
            }).toLowerCase().trim().replace(/\.$/, "");
            // const commands = {
            //     [listeningText]: onCommandTrigger
            // };
            // annyang.removeCommands();
            // console.log('add commands: ', commands);
            // annyang.addCommands(commands);
            setTimeout(function(){
                annyang.resume();
                microphoneElement.src = MICROPHONE_ICON_URL;
                microphoneElement.style.display = "block";
            }, 200);
        }

        function initSpeechToTextEngine() {
            if (!annyang) {
                console.log('initSpeechToTextEngine');
                $.getScript("https://cdnjs.cloudflare.com/ajax/libs/annyang/2.6.1/annyang.min.js", function() {
                    console.log("annyang.min.js loaded: ", annyang);
                    annyang.addCallback('result', endListening);
                    annyang.start({ autoRestart: true, paused: true });
                });
                $.getScript("https://unpkg.com/string-similarity@4.0.4/umd/string-similarity.min.js", function() {
                    console.log("string-similarity loaded");
                });
            }
        }

        var _playSpeakingSound = function() {
            var currentSound;
            while (!currentSound && soundSpeakQueue && soundSpeakQueue.length) {
                var tmpSound = soundSpeakQueue.shift();
                var text = tmpSound.alt || tmpSound.text;
                if (text && !text.startsWith('You:')) {
                    currentSound = tmpSound;
                }
            }
            if (currentSound) {
                if (currentSound.alt !== 'sys_microphone' && (currentSound.alt || currentSound.text)) {
                    var soundName = slugify(currentSound.alt || currentSound.text);
                    _play(soundName);
                } else if (currentSound.htmlElement) {
                    listeningElement = currentSound;
                    startListening(currentSound.htmlElement, currentSound.title);
                }
            } else {
                setTimeout(function(){
                    h5pInstance.nextSlide();
                }, 500);
            }
        }

        var playSpeakingSounds = function(soundElements) {
            soundSpeakQueue = soundElements;
            clearTimeout(playTimeout);
            let newTextList;
            if (soundElements && soundElements.length) {
                newTextList = soundElements.filter(function(sound){
                    return sound.alt !== 'sys_microphone' && (sound.alt || sound.text);
                }).map(function(sound){
                    return sound.alt || sound.text;
                })
            }
            _playSpeakingSound();
            if (newTextList && newTextList.length) {
                createSound(newTextList);
            }
        }

        var parseHtml = function (str) {
            return str.replace(/&#([0-9]{1,3});/gi, function(match, numStr) {
                var num = parseInt(numStr, 10); // read num as normal number
                return String.fromCharCode(num);
            });
        }

        var processMicrophoneSlide = function(currentSlide) {
            _visibleNavigator(false);
            var slideElements = currentSlide.elements;
            var soundElements = slideElements.map(function (element) {
                var params = element.action.params;
                var htmlElement;
                if (params.alt === 'sys_microphone' && microphoneElements) {
                    microphoneElements.forEach(function(elem) {
                        if (parseHtml(elem.title) === parseHtml(params.title)) {
                            htmlElement = elem;
                        }
                    });
                }
                return {
                    alt: params.alt,
                    text: removeHtml(params.text),
                    title: params.title,
                    x: element.x,
                    y: element.y,
                    htmlElement: htmlElement
                };
            });
            console.log('soundElements: ', soundElements);
            soundElements.sort(function(a, b){
                // return Math.hypot(a.x, a.y) - Math.hypot(b.x, b.y);
                if (a.y === b.y) {
                    return a.x - b.x;
                }
                return a.y - b.y;
            });
            playSpeakingSounds(soundElements);
        }

        function _visibleNavigator(isShow) {
            var navigators = document.querySelectorAll('[class="h5p-cp-navigation"]');
            if (navigators && navigators.length) {
                navigators[0].style.display = isShow ? "block" : "none";
            }
            var nextSlides = document.querySelectorAll('[class="h5p-footer-button h5p-footer-next-slide"]');
            if (nextSlides && nextSlides.length) {
                nextSlides[0].style.display = isShow ? "inline-block" : "none";
            }
        }

        function init() {
            microphoneElements = document.querySelectorAll('[alt="sys_microphone"]');
            console.log('microphoneElements: ', microphoneElements);
            if (microphoneElements && microphoneElements.length) {
                microphoneElements.forEach(function(element) {
                    element.style.display = "none";
                });
                if (!annyang) {
                    initSpeechToTextEngine();
                }
            }

            soundQueue = [];
            soundSpeakQueue = [];
            if (annyang) {
                // annyang.pause();
                annyang.abort();
            }
            _visibleNavigator(true);
        }

        console.log('H5P: ', H5P);
        var h5pInstances = H5P.instances;
        console.log('h5pInstances: ', h5pInstances);
        var h5pInstance = h5pInstances[0];
        console.log('h5pInstance: ', h5pInstance);
        var slides = h5pInstance.slides;
        console.log('slides: ', slides);
        $.getScript("https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.3/howler.min.js", function() {
            console.log("howler.min.js loaded: ", Howl);
            if (slides && slides.length > 0) {
                var firstSlide = slides[h5pInstance.getCurrentSlideIndex()];
                console.log('firstSlide: ', firstSlide);
                playSoundFromSlide(firstSlide);
            }
        });

        H5P.externalDispatcher.on('xAPI', function (event) {
            console.log('H5P.externalDispatcher: ', event);
            clearTimeout(pageTimeout);
            clearTimeout(playTimeout);
            clearTimeout(initTimeout);
            initTimeout = setTimeout(function() {
                init();
            }, 500);
            var verb = event.data.statement.verb.id;
            console.log('verb: ', verb);
            if (verb === "http://adlnet.gov/expapi/verbs/answered") {
                return processAnsweredSlide(event);
            }
            pageTimeout = setTimeout(function() {
                console.log('current index: ', h5pInstance.getCurrentSlideIndex());
                const currentSlide = slides[h5pInstance.getCurrentSlideIndex()];
                var slideElements = currentSlide.elements;
                console.log('currentSlide: ', currentSlide);

                var multiChoiceElements = slideElements.filter(function(element){
                    return element.action.metadata.contentType === "Multiple Choice";
                });
                console.log('multiChoiceElements: ', multiChoiceElements);
                if (multiChoiceElements && multiChoiceElements.length) {
                    return processMultichoiceSlide(multiChoiceElements);
                }

                var fillBlankElements = slideElements.filter(function(element){
                    return element.action.library === "H5P.Blanks 1.12";
                });
                console.log('fillBlankElements: ', fillBlankElements);
                if (fillBlankElements && fillBlankElements.length) {
                    return processFillBlankSlide(fillBlankElements);
                }

                var microphoneImages = slideElements.filter(function(element){
                    return element.action.params.alt === "sys_microphone";
                });

                if (microphoneImages && microphoneImages.length > 0) {
                    return processMicrophoneSlide(currentSlide);
                }
                playSoundFromSlide(currentSlide);
            }, 1000);
        });
    });
})(H5P.jQuery);
