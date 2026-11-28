"""Builds words/<lang>.json from the output of check_words.py.
   python build_words.py <work dir> <project dir>
   The minimum of a puzzle is measured on the whole dictionary, so nobody can beat it,
   and at least one solution of that length uses only common words."""
import collections, json, os, random, sys
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from filters import load, DROP, LOAN

S, OUT = sys.argv[1], sys.argv[2]
LENGTHS, LEVELS, PER_LEVEL = (5, 6), (4, 6, 8), 600
cap = {4: 2, 6: 3, 8: 6}   # how many puzzles may share a start or an end word
Z_END, Z_PATH = 3.5, 2.8

def graph(words):
    buckets = collections.defaultdict(list)
    for w in words:
        for i in range(len(w)): buckets[w[:i] + '_' + w[i + 1:]].append(w)
    g = {w: [] for w in words}
    for ws in buckets.values():
        for a in ws:
            for b in ws:
                if a != b: g[a].append(b)
    return g

def bfs(g, start):
    dist = {start: 0}; queue = collections.deque([start])
    while queue:
        u = queue.popleft()
        for v in g[u]:
            if v not in dist: dist[v] = dist[u] + 1; queue.append(v)
    return dist

for lang in ('it', 'en', 'es', 'fr', 'de'):
    data, bad, names, proper = load(S, lang)
    drop, loan = set(DROP[lang].split()) | bad, set(LOAN[lang].split())
    res = {'words': {}, 'common': {}, 'puzzles': {}}
    for L in LENGTHS:
        allw = [w for w in data if len(w) == L and w not in drop]
        g = graph(allw)
        # The biggest connected part: every word that can be played can always reach the end
        seen, best = set(), []
        for w in allw:
            if w not in seen:
                part = list(bfs(g, w)); seen.update(part)
                if len(part) > len(best): best = part
        main = set(best)
        fine = lambda w: w in main and w not in names and w not in proper and w not in loan
        common = sorted((w for w in allw if fine(w) and data[w][1] >= Z_PATH), key=lambda w: -data[w][1])
        cset = set(common)
        ends = [w for w in common if data[w][1] >= Z_END]
        endset = set(ends)
        rest = sorted((w for w in allw if w not in cset), key=lambda w: -data[w][1])
        cg = {w: [v for v in g[w] if v in cset] for w in common}
        rng = random.Random(f'{lang}{L}')
        rng.shuffle(ends)
        found = {n: [] for n in LEVELS}
        used = {n: collections.Counter() for n in LEVELS}
        for s in ends:
            full, com = bfs(g, s), bfs(cg, s)
            for n in LEVELS:
                d = n + 1
                if len(found[n]) >= PER_LEVEL or used[n][s] >= cap[n]: continue
                cands = [w for w, x in com.items() if x == d and full.get(w) == d and w in endset and used[n][w] < cap[n]]
                rng.shuffle(cands)
                for t in cands[:cap[n]]:
                    found[n].append(f'{s} {t}'); used[n][s] += 1; used[n][t] += 1
            if all(len(v) >= PER_LEVEL for v in found.values()): break
        res['words'][L] = ' '.join(common + rest)
        res['common'][L] = len(common)
        res['puzzles'][L] = {n: v[:PER_LEVEL] for n, v in found.items()}
        print(lang, L, 'words', len(allw), 'common', len(common), 'ends', len(endset), {n: len(v) for n, v in found.items()}, flush=True)
    with open(f'{OUT}/words/{lang}.json', 'w', encoding='utf-8') as f:
        json.dump(res, f, ensure_ascii=False, separators=(',', ':'))
