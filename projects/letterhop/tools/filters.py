import json, re, zipfile
EXTRA_BAD = {
 'it': 'cazzo cazzi cazzata cazzate culo culi merda merde troia troie figa fighe puttana mignotta negro negri negra negre frocio froci coglione coglioni stronzo stronza stronzi scopata sborra pompino tette zoccola zoccole minchia nazi nazista vagina vagine cesso uccidi stupro stupra',
 'en': 'nigger nigga negro faggot fagot cunts cunt bitch bitches whore whores slut sluts shit shits fuck fucks penis vagina pussy dicks cocks tits boobs nazis rapes raped rapist',
 'es': 'puta putas puto putos mierda polla pollas pene penes vagina negro negra negros negras maricon marica joder follar coger tetas culos zorra zorras nazi nazis violar violan viola',
 'fr': 'merde merdes putain putes salope salopes bite bites negre negres pedes penis vagin baiser niquer connard connasse nichon nazis nazi violer viole violee seins',
 'de': 'ficken fotze hure huren arsch neger negern nutte nutten wichser schlampe penis vagina titten nazis pornos porno lesben schwul schwule juden',
}
# Normal words that the lists of bad words catch because of a slang meaning
SAFE = {
 'it': 'balle cagna cozza crepa ebrei ebreo monta palle pesce pompa porca porco regina tirare vacca',
 'en': 'escort eunuch snatch sucks',
 'es': 'coger concha drogas idiota maciza orina viola violan',
 'fr': 'baiser folle gerbe gueule jouir seins tanche',
 'de': 'bonze fratze kimme nackt',
}

def load(S, lang):
    data = json.load(open(f'{S}/checked-{lang}.json'))
    bad = set(x.strip().lower() for x in open(f'{S}/dl/lists/bad-{lang}.txt')) | set(EXTRA_BAD[lang].split())
    bad -= set(SAFE[lang].split())
    names = set(x.strip().lower() for x in open(f'{S}/dl/lists/names.txt'))
    dics = {'en': ['en-GB', 'en']}.get(lang, [lang])
    entries = {}
    for d in dics:
        for line in open(f'{S}/dl/hun/{d}/index.dic', encoding='utf-8', errors='ignore'):
            w, _, fl = line.strip().partition('/')
            entries.setdefault(w, fl.split()[0] if fl else '')
    proper = set()   # never a start, an end or a solution word
    if lang == 'de':
        ot = set()
        for line in open(f'{S}/dl/lists/ot/openthesaurus.txt', encoding='utf-8'):
            if line.startswith('#'): continue
            for term in line.strip().split(';'):
                term = re.sub(r'\(.*?\)', '', term).strip().lower()
                if ' ' not in term: ot.add(term)
        for w, fl in entries.items():
            if w[:1].isupper() and set(fl) <= set('STm') and w.lower() not in ot: proper.add(w.lower())
    else:
        proper = {w.lower() for w in entries if w[:1].isupper()}
    return data, bad, names, proper

# Reviewed by hand. DROP: names, places and vulgar words, out of the game. LOAN: valid, but never a start, an end or a solution word.
DROP = {
 'it': 'alter anglo bossi carpi cipro corea dante guido india libia longo luigi malta marco nepal rizzo rocco romeo tesla tibet seghe sfiga fotte frega porno xviii xxiii apollo aquino biella castel castro cristo fausto foggia france martin mattia mauro cesena mozart newton orsini palmer panama piceno rivera stuart tullio uffizi cagate sperma fighi figata stupri sbarre faust ardea dildo tamil boeri vitro urano orali anale anali pippa pippe scoreggia',
 'en': 'nazi jesus satan fucked fucker fucks fucking shitty shite pissed pisses arses asses bitchy dildo wanker wanks horny sperm semen anal anus rectum nudes',
 'es': 'castro pilar rocio kelvin linden pascal ringo japon pubis cagar cagas meado mear pedos dildo anal ano',
 'fr': 'japon nique niques niquer prout baise baises baisee bander pisse pisser chier chie cul culs couille couilles enculer dildo anal anale pede',
 'de': 'aalen adams athen bayer china chile donau emden fritz freud georg ghana grimm genua horst japan jemen jesus kairo karls kenia kongo korea lloyd louis mainz malta milan mosel nepal oscar paris peter pfalz polen rhein simon syrer tokio verdi wales weber wiens asien aachen afrika ankara apollo bagdad bayern berlin bremen bremer carlos chinas dachau dublin europa goethe hannes hawaii hermes indien irland island israel japans jochen kanada kosovo libyen london madrid merkur mexiko monaco moskau ostsee passau peking peters polens prager syrien taiwan trumps wagner werder zypern basler bonner genfer kieler wiener elsass tirol worms colmar haifa dalai dildo fickt geile geilen geiler geiles hoden kotzt kotzen sperma penner busen kondom kokain heroin satan stasi',
}
LOAN = {
 'it': 'alias blitz blues bonus check click clown coach cover crack dance derby drink drive ebook email focus frame front facto indie input jeans jolly lobby manga match metal model modus monde mouse ninja party pixel poker promo queen rally range ratio relax round rugby scout shirt shock snack sound space spray staff stage stand stars start stock suite super sushi trans trend troll trust tutor tweet water yacht assist backup banner basket bikini bridge budget buffet bunker bureau camper campus canyon casual chance client corner corpus design diesel driver editor empire escort export gadget garage garden gossip hacker hockey horror jersey junior killer leader madame market marine master medium office online poster prince puzzle racing random ranger rapper record remake script secret selfie senior sequel serial server signor single sketch slogan spider spread spring status stereo summit tablet target tennis ticket troupe tunnel yogurt zombie',
 'en': '',
 'es': '',
 'fr': '',
 'de': 'basic blogs bands chefs clips clubs cafes deals first least rates tools mails shops shows songs bring facto excel linux major rapid royal stars teams tests hobbys partys sports sounds trends summer future police office double player review spring',
}
