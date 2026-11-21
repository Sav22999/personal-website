# Dizionari di LetterHop

I file `<lingua>.json` sono generati da `tools/build_words.py` (vedi il README). Fonti e licenze:

- **Frequenze delle parole**: [wordfreq](https://github.com/rspeer/wordfreq), dati CC BY-SA 4.0.
- **Controllo ortografico Hunspell** (raccolta [wooorm/dictionaries](https://github.com/wooorm/dictionaries)):
  italiano (GPL-3.0), inglese (MIT/BSD), spagnolo (GPL/LGPL/MPL), francese (MPL-2.0), tedesco (GPL-2.0/OASIS).
- **Elenchi di parole
  candidate**: [napolux/paroleitaliane](https://github.com/napolux/paroleitaliane), [davidak/wortliste](https://github.com/davidak/wortliste),
  an-array-of-english/french/spanish-words.
- **Sinonimi tedeschi** per riconoscere i nomi propri: [OpenThesaurus](https://www.openthesaurus.de/) (LGPL / CC BY-SA).
- **Parole volgari da
  escludere**: [LDNOOBW](https://github.com/LDNOOBW/List-of-Dirty-Naughty-Obscene-and-Otherwise-Bad-Words) (CC BY 4.0),
  più liste riviste a mano in `tools/filters.py`.
