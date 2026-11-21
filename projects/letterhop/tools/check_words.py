# Candidates (frequency list + big lists) -> words accepted by Hunspell, folded to A-Z, 4-6 letters.
import sys, json, re, unicodedata, time
from wordfreq import top_n_list, zipf_frequency
from spylls.hunspell import Dictionary
lang, S = sys.argv[1], sys.argv[2]
DL = S + '/dl'
def fold(w):
    if lang == 'de' and re.search('[äöüß]', w): return None
    if lang == 'es' and 'ñ' in w: return None
    if 'œ' in w or 'æ' in w: return None
    f = ''.join(c for c in unicodedata.normalize('NFD', w) if unicodedata.category(c) != 'Mn')
    return f if re.fullmatch('[a-z]{4,6}', f) else None
cands = set(w.lower() for w in top_n_list(lang, 2000000, wordlist='large'))
extra = {'it': ['it-280k.txt'], 'de': ['de-wortliste.txt']}.get(lang, [])
for f in extra: cands |= set(x.strip().lower() for x in open(f'{DL}/lists/{f}', encoding='utf-8', errors='ignore'))
js = {'en': 'an-english.json', 'fr': 'an-french.json', 'es': 'an-spanish.json'}.get(lang)
if js: cands |= set(x.lower() for x in json.load(open(f'{DL}/lists/{js}')))
cands = [w for w in cands if fold(w)]
print(lang, 'candidates', len(cands), flush=True)
dicts = [Dictionary.from_files(f'{DL}/hun/{d}/index') for d in ({'en': ['en-GB', 'en']}.get(lang, [lang]))]
def ok(w):
    for d in dicts:
        if d.lookup(w): return True
        if lang == 'de' and d.lookup(w.capitalize()): return True
    return False
out = {}; t = time.time()
for i, w in enumerate(cands):
    if i % 20000 == 0: print(lang, i, int(time.time() - t), flush=True)
    if ok(w):
        f = fold(w); z = zipf_frequency(w, lang, wordlist='large')
        if f not in out or z > out[f][1]: out[f] = (w, z)
json.dump(out, open(f'{S}/checked-{lang}.json', 'w'), ensure_ascii=False)
print(lang, 'DONE', len(out), flush=True)
