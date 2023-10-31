console.log("H5P: ", H5P);
var Howl;

(function ($) {
    $(document).ready(function () {
        var soundQueue = [];
        var audioElement = document.createElement("audio");

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
            setTimeout(function(){
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

        var compareText = function(a, b) {
            return a.toLowerCase().trim() ===  b.toLowerCase().trim();
        }

        var playSingleSound = function(text, soundName) {
            soundQueue = [soundName];
            createSound([text]);
            _playSound();
        }

        var playMultipleSounds = function(actionTextList, soundNames) {
            soundQueue = soundNames.filter(function(name){
                return name && name.length;
            });
            _playSound();
            if (actionTextList && actionTextList.length) {
                actionTextList = actionTextList.filter(function(name){
                    return name && name.length;
                });
                createSound(actionTextList);
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

        var getDifferenceNodeFromState = function (oldState, newState) {
            var stateLength = newState.length;
            var index, newAnswer;
            for (var i = 0; i < stateLength; i++) {
                if (newState[i] !== oldState[i]) {
                    index = i;
                    newAnswer = newState[i];
                    break;
                }
            }
            return {
                index: index,
                text: newAnswer
            };
        };

        var h5pInstances = H5P.instances;
        var h5pInstance = h5pInstances[0];
        console.log('h5pInstance: ', h5pInstance);
        var h5pInstanceContent = H5P.getContentForInstance(h5pInstance.id);
        var jsonContentString = h5pInstanceContent['jsonContent'];
        console.log('jsonContentString: ', jsonContentString);
        var jsonContent = JSON.parse(jsonContentString);
        console.log('jsonContent: ', jsonContent);
        var cards = jsonContent.cards;
        console.log('cards: ', cards);
        var gameState = [];

        $.getScript("https://cdnjs.cloudflare.com/ajax/libs/howler/2.2.3/howler.js", function() {
            console.log("howler.min.js loaded: ", Howl);
        });

        H5P.externalDispatcher.on('xAPI', function (event) {
            console.log('H5P.externalDispatcher: ', event);
            var h5pInstances = H5P.instances;
            var h5pInstance = h5pInstances[0];
            console.log('h5pInstance: ', h5pInstance);
            var gameAnswers = h5pInstance.answers;
            console.log('gameState: ', gameState);
            console.log('gameAnswers: ', gameAnswers);
            var newNode = getDifferenceNodeFromState(gameState, gameAnswers);
            console.log('newNode: ', newNode);
            gameState = gameAnswers.slice()  ;
            if (newNode.index > -1) {
                var currentCard = cards[newNode.index];
                console.log('currentCard: ', currentCard);
                if (compareText(currentCard.answer, newNode.text)) {
                    var slugText = slugify(newNode.text);
                    console.log('slugText: ', slugText);
                    playSingleSound(newNode.text, slugText);
                }
            }
        });
    })
})(H5P.jQuery);
