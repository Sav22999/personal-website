/*
 * Generates questions from every pattern and checks that each one is fair: node tools/check_questions.js
 * One right answer, options all different, a text for every key in the five languages, difficulty in range, and
 * levels whose difficulty grows without repeating anything.
 */
'use strict';

const path = require('path');
const dir = path.join(__dirname, '..');
globalThis.window = globalThis;
globalThis.document = {documentElement: {}};
globalThis.navigator = {language: 'en'};
['i18n.js', 'i18n-puzzles.js', 'riddles.js', 'riddles-more.js', 'twemoji.js', 'engine.js', 'puzzles.js', 'mascots.js', 'help.js'].forEach(f => require(path.join(dir, f)));
require(path.join(dir, 'tools', 'question_checks.js'))(console.log, process.exit);
