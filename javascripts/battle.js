			var current_word;
			var current_answers;
			var word_forms;
			var score = 0;
			var playerDamage = 1;
			var victory = 1000;
			var hitStreak = 0;
			var currentMonster;
			var currentMonsters;
			var currentDifficulty = 0;
			var points = [10,25,50];
			var monsters = [];
			var kills = 0;

			var easyMonster = {
				hp: 10,
				xp: 10,
				question: function() pickNewWord(0),
				success: function(a) isCorrect(), 
				hitSound: new Audio("/sound/hit.mp3"),
				deathAnim: function () animateDeath("m"),
				appearAnim: function () animateAppearance("m"),
				images: ['easymonster.png','easymonster2.png','easymonster3.png']};

			var hardMonster = {
				hp: 10,
				xp: 10,
				question: function() pickNewWord(1),
				success: function(a) isCorrect(), 
				hitSound: new Audio("/sound/hit.mp3"),
				deathAnim: function () animateDeath("m"),
				appearAnim: function () animateAppearance("m"),
				images: ['swordsman.png','swordsman2.png','swordsman3.png']};

			var currentMonsterType = easyMonster;
			var punch1 = new Audio("/sound/hit.mp3");
			var punch2 = new Audio("/sound/hit2.mp3");
			var tada = new Audio("/sound/tada.wav");

			function animateAppearance(id,image) {
				document.getElementById('location').innerHTML += "<img src='/img/" + currentMonster.images[randomIntFromInterval(0,currentMonster.images.length - 1)] + "' id=m class=monster />";
				currentMonster.dom = document.getElementById('m');
				currentMonster.dom.style.left = '300px';
				currentMonster.dom.style.top = '360px';
				setTimeout(function() {
					new Effect.Appear("m");
					new Effect.Move("m",{x:515,y:330,mode: 'absolute'})
				},1000);
			}

			function animateDeath(id) {
				new Effect.Fade(id,{
					duration: 1.5, 
					queue: 'end', 
					afterFinish: function(){
							var e = document.getElementById(id);
							e.parentNode.removeChild(e);
						}});
			}

			var word_forms_easy = [
			["gladius",["nom"]],
			["gladie",["voc"]],
			["gladiī",["gen"]],
			["gladiō",["dat","abl"]],
			["gladium",["acc"]]];

			var word_forms_med = [
			["gladius",["nom"]],
			["gladie",["voc"]],
			["gladiī",["gen","nom_pl","voc_pl"]],
			["gladiō",["dat","abl"]],
			["gladium",["acc"]],
			["gladiōrum",["gen_pl"]],
			["gladiōs",["acc_pl"]],
			["gladiīs",["dat_pl","abl_pl"]]];

			var abbrevs = {
			"nom":"nominative singular",
			"gen":"genitive singular",
			"voc":"vocative singular",
			"dat":"dative singular",
			"acc":"accusative singular",
			"abl":"ablative singular",
			"nom_pl":"nominative plural",
			"gen_pl":"genitive plural",
			"voc_pl":"vocative plural",
			"dat_pl":"dative plural",
			"acc_pl":"accusative plural",
			"abl_pl":"ablative plural"};

			function unselected_style(b) {
				b.selected = false;
				b.style.color = "orange";
				b.style.borderColor = "orange";
				b.style.backgroundColor = "#333333";
			}

			function selected_style(b) {
				b.selected = true;
				b.style.color = "white";
				b.style.borderColor = "orange";
				b.style.backgroundColor = "#CC6900";
			}

			function button_clicked(bt) {				
				if(bt.selected) {
					unselected_style(bt);
				} else {
					selected_style(bt);
				}
			}

			function pickNewWord(difficulty) {
				if(difficulty == 0) {
					word_forms = word_forms_easy;
					document.getElementById("plural").style.display="none";
					document.getElementById("singular").style.cssFloat="none";
					document.getElementById("singular").style.styleFloat="none";
					document.getElementById("singular").style.overflow="hidden";
					document.getElementById("singular").style.marginLeft="auto";
					document.getElementById("singular").style.marginRight="auto";
				} else if (difficulty == 1) {
					word_forms = word_forms_med;
					document.getElementById("plural").style.display="block";
					document.getElementById("singular").style.cssFloat="left";
					document.getElementById("singular").style.styleFloat="left";
					document.getElementById("singular").style.overflow="hidden";
					document.getElementById("singular").style.marginLeft="3%";
					document.getElementById("singular").style.marginRight="3%";
				}
				var old_word = current_word;
				do {
				var current_form_index = randomIntFromInterval(0,word_forms.length - 1);
				current_word = word_forms[current_form_index][0];
				current_answers = word_forms[current_form_index][1];
				} while (old_word == current_word);
				document.getElementById("question").innerHTML = "<b>In which case(s) is this word?</b><br/>" + current_word;
			}

			function toUnorderedList(a,className) {
				function tagStr(s,t) { return "<" + t + ">" + s + "</" + t + ">"; }
				var taggedList = a.reduce(function(x,y) x + tagStr(y,"li"),"");
				return "<ul class=" + className + ">" + taggedList + "</ul>";
			}


			function newMonster() {
				currentMonster = _.clone(currentMonsterType);
				currentMonster.question();
				currentMonster.appearAnim();
				updateScore();
			}

			function changeDifficulty(d) {
				if(d == 1) {
					currentMonsterType = hardMonster;
				} else {
					currentMonsterType = easyMonster;
				}
				currentMonster.deathAnim();
				setTimeout(function() {newMonster();},2000);
			}

			function getMatchingValues(a,dict) {
				return a.map(function(x) dict[x]);
			}

			function highlightCorrectIncorrect(correct,wrong,forgotten) {
				a = [[correct,"correct"],[wrong,"wrong"],[forgotten,"missing"]];
				var result = "";
				for(var x = 0;x < a.length;x++) {
					l = a[x][0]
					if(l.length != 0) {
						var v = getMatchingValues(l,abbrevs);
						result += a[x][1] + "<br/>";
						result += toUnorderedList(v,a[x][1]);
					}
				}
				document.getElementById("lastq").innerHTML = "<h2>" + current_word + "</h2>" + result;
				resetSelections();
			}

			function resetSelections() {
				l = document.getElementsByClassName("answer");
				for(var x = 0; x < l.length; x++) {
					l[x].selected = false;
					unselected_style(l[x]);
				}
			}

			function updateScore() {
				document.getElementById("score").innerHTML = "Enemy HP: " + currentMonster.hp + " / " + currentMonsterType.hp;
				document.getElementById("pbar_fill").style.width = String(Math.floor(140 * (currentMonster.hp / currentMonsterType.hp))) + "px";
			}


			function rotateMonsters() {
				currentMonster.deathAnim();
				currentMonster.appearAnim();
			}

			function monster_struck_effects() {
				punch1.play();
				new Effect.Move("hero",{duration: .05, y: -10});
				new Effect.Move("hero",{duration: .05, y: 10, queue: 'end'});
				new Effect.Shake("m", {duration: .25, distance: 10, queue: 'end'})
				new Effect.Appear("monsterhit",{duration: .25, queue: 'end'});
				new Effect.Fade("monsterhit",{duration: .25, queue: 'end'});
			}

			function isCorrect() {
				var selected = Array.prototype.slice.call(document.getElementsByClassName("answer"))
					.filter(function(a) a.selected)
					.map(function(a) a.id);
					
				var wrong   = _.difference(selected,current_answers);
				var forgot  = _.difference(current_answers,selected);
				var correct = _.intersection(selected,current_answers);
				var allCorrect = (wrong.length + forgot.length) == 0; 
				return allCorrect;	
			}

			function check_answers() {
				var selected = Array.prototype.slice.call(document.getElementsByClassName("answer"))
					.filter(function(a) a.selected)
					.map(function(a) a.id);
					
				var wrong   = _.difference(selected,current_answers);
				var forgot  = _.difference(current_answers,selected);
				var correct = _.intersection(selected,current_answers);
				var allCorrect = (wrong.length + forgot.length) == 0; 

				if(allCorrect) {
					hitStreak++;
					if(hitStreak == 2) {
						playerDamage = 2;
						document.getElementById("streak").innerHTML = "2X Streak! <br/>Double Damage!";
					}
					if(hitStreak == 5) {
						playerDamage = 5;
						document.getElementById("streak").innerHTML = "5X Streak! <br/>5X Damage!";
					}
					currentMonster.hp -= playerDamage;
					monster_struck_effects();
					if(currentMonster.hp <= 0) {
						currentMonster.deathAnim();
						setTimeout(function() {newMonster();},2000);
					}
					if(score > 1000) { score = 1000; tada.play(); }
					updateScore();
				} else {
					hitStreak = 0;
					playerDamage = 1;
					document.getElementById("streak").innerHTML = "";
					punch2.play();
					new Effect.Shake("hero")
					new Effect.Appear("herohit",{duration: .25});
					new Effect.Fade("herohit",{duration: .15, queue: 'end'});
				}
				highlightCorrectIncorrect(correct,wrong,forgot);
				currentMonster.question();
			}


