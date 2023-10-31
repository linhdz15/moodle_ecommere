console.log("customDragDrop H5P: ", H5P);
var Howl;

(function ($) {
    $(document).ready(function () {
        var soundQueue = [];
        var audioElement = document.createElement("audio");
        var soundTimeout;

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
            soundTimeout = setTimeout(function(){
                if (soundQueue && soundQueue.length) {
                    _playSound();
                }
            }, 800);
        };

        var _playSound = function() {
            if (soundQueue && soundQueue.length) {
                var currentSoundName = soundQueue.shift();
                if (currentSoundName && currentSoundName.length) {
                    var source = "https://cdn.esl4u.net/sound/" + currentSoundName + ".wav";
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

        var playSingleSound = function(text, soundName) {
            soundQueue = [soundName];
            createSound([text]);
            clearTimeout(soundTimeout);
            _playSound();
        }

        var playMultipleSounds = function(actionTextList, soundNames) {
            soundQueue = soundNames.filter(function(name){
                return name && name.length;
            });
            clearTimeout(soundTimeout);
            _playSound();
            if (actionTextList && actionTextList.length) {
                actionTextList = actionTextList.filter(function(name){
                    return name && name.length;
                });
                createSound(actionTextList);
            }
        }

        var h5pInstances = H5P.instances;

        var h5pInstance = h5pInstances[0];
        console.log('h5pInstance: ', h5pInstance);
        var dropZones = h5pInstance.dropZones;
        console.log('dropZones: ', dropZones);
        var draggables = h5pInstance.draggables;
        console.log('draggables: ', draggables);
        var correctDZs = h5pInstance.correctDZs;
        console.log('correctDZs: ', correctDZs);
        var draggableLength = draggables.length;
        var gameState = new Array(draggableLength);
        console.log('gameState: ', gameState);

        $.getScript("https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.3/howler.js", function() {
            console.log("howler.min.js loaded: ", Howl);
        });

        var getDifferenceNodeFromState = function (oldState, newState) {
            var stateLength = oldState.length;
            var changedIdx;
            var changedDropzone;
            for (var i = 0; i < stateLength; i++) {
                if (newState[i] && ( !oldState[i] || (newState[i][0].dz !== oldState[i][0].dz) ) ) {
                    changedIdx = i;
                    changedDropzone = newState[i][0].dz;
                    break;
                }
            }
            return {
                index: changedIdx,
                dropzonIndex: changedDropzone
            };
        };

        var checkCorrectAnswer = function (dropzoneId, draggableId) {
            return correctDZs[draggableId].includes(dropzoneId);
        };

        function slugify(text) {
            if (text && text.length) {
                var plainText = text.replace(/&nbsp;/g, '').replace('A:', '').replace('B:', '').replace(/<[^>]+>/g, '').trim();
                return plainText.toLowerCase()
                    .replace(/ /g,'-')
                    .replace(/[^\w-]+/g,'');
            }
            return '';
        }

        H5P.externalDispatcher.on('xAPI', function (event) {
            console.log('externalDispatcher: ', event);
            var currentState = h5pInstance.getCurrentState();
            console.log('currentState: ', currentState);
            var currentAnswers = currentState.answers;
            console.log('currentAnswers: ', currentAnswers);
            var currentGameState = new Array(draggableLength);
            for (var i = 0; i < currentAnswers.length; i++) {
                currentGameState[i] = currentAnswers[i];
            }
            console.log('gameState: ', gameState);
            console.log('currentGameState: ', currentGameState);
            var updatedNode = getDifferenceNodeFromState(gameState, currentGameState);

            console.log('updatedNode: ', updatedNode);
            gameState = currentGameState;
            var draggableId = updatedNode.index;
            var dropzoneId = updatedNode.dropzonIndex;
            if (draggableId > -1 && dropzoneId > -1) {
                var isCorrect = checkCorrectAnswer(dropzoneId, draggableId);
                console.log('isCorrect: ', isCorrect);
                if (isCorrect) {
                    var correctDraggable = draggables[draggableId];
                    console.log('correctDraggable: ', correctDraggable);
                    var correctDropzone = dropZones[dropzoneId];
                    console.log('correctDropzone: ', correctDropzone);
                    var text;
                    if (correctDropzone.showLabel && correctDropzone.label) {
                        text = correctDropzone.label;
                    } else {
                        text = correctDraggable.type.params.alt || correctDraggable.type.params.text;
                    }
                    console.log('text: ', text);
                    if (text && text.length) {
                        var slugText = slugify(text);
                        console.log('slugText: ', slugText);
                        playSingleSound(text, slugText);
                    }
                }
            }
        });
    })
})(H5P.jQuery);
