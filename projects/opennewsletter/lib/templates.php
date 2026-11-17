<?php
/**
 * OpenNewsletter — the built-in templates (ids "b:…"). Groups save their own in the templates table.
 * Texts are placeholders in the language of the person who opens them (en / it).
 */

declare(strict_types=1);

require_once __DIR__ . '/render.php';

function builtin_templates(string $lang): array
{
    $it = $lang === 'it';
    $T = fn(string $en, string $itText) => $it ? $itText : $en;
    $theme = fn(array $o = []) => array_merge(default_theme(), $o);
    $id = 0;
    $b = function (string $type, array $o) use (&$id) {
        $id++;
        return array_merge(['id' => 'tpl' . str_pad((string)$id, 4, '0', STR_PAD_LEFT), 'type' => $type], $o);
    };
    return [
        ['id' => 'b:blank', 'name' => $T('Blank', 'Vuoto'), 'description' => $T('Start from an empty page', 'Parti da una pagina vuota'),
            'doc' => ['theme' => $theme(), 'blocks' => [
                $b('heading', ['text' => $T('Your title', 'Il tuo titolo'), 'level' => 1, 'align' => 'left']),
                $b('text', ['html' => '<p>' . $T('Write here…', 'Scrivi qui…') . '</p>', 'align' => 'left']),
            ]]],
        ['id' => 'b:announce', 'name' => $T('Announcement', 'Annuncio'), 'description' => $T('One big piece of news with a button', 'Una grande notizia con un pulsante'),
            'doc' => ['theme' => $theme(['accent' => '#5B3DF5']), 'blocks' => [
                $b('image', ['src' => '', 'alt' => '', 'href' => '', 'width' => 100, 'align' => 'center', 'rounded' => true]),
                $b('heading', ['text' => $T('Something new is here', 'È arrivata una novità'), 'level' => 1, 'align' => 'center']),
                $b('text', ['html' => '<p>' . $T('Tell your readers in two or three sentences what changes for them and why it matters.', 'Racconta ai tuoi lettori in due o tre frasi cosa cambia per loro e perché è importante.') . '</p>', 'align' => 'center']),
                $b('button', ['label' => $T('Find out more', 'Scopri di più'), 'href' => 'https://', 'align' => 'center', 'color' => '', 'full' => false]),
                $b('divider', ['color' => '']),
                $b('social', ['links' => [], 'align' => 'center']),
            ]]],
        ['id' => 'b:digest', 'name' => $T('Digest', 'Raccolta'), 'description' => $T('Several stories, one card each', 'Più notizie, una scheda ciascuna'),
            'doc' => ['theme' => $theme(['accent' => '#0E8A6A', 'bg' => '#EEF5F1']), 'blocks' => [
                $b('heading', ['text' => $T('This week', 'Questa settimana'), 'level' => 1, 'align' => 'left']),
                $b('text', ['html' => '<p>' . $T('A short introduction to this issue.', 'Una breve introduzione a questo numero.') . '</p>', 'align' => 'left']),
                $b('card', ['src' => '', 'title' => $T('First story', 'Prima notizia'), 'html' => '<p>' . $T('A few lines about it.', 'Qualche riga per raccontarla.') . '</p>', 'label' => $T('Read', 'Leggi'), 'href' => 'https://']),
                $b('card', ['src' => '', 'title' => $T('Second story', 'Seconda notizia'), 'html' => '<p>' . $T('A few lines about it.', 'Qualche riga per raccontarla.') . '</p>', 'label' => $T('Read', 'Leggi'), 'href' => 'https://']),
                $b('card', ['src' => '', 'title' => $T('Third story', 'Terza notizia'), 'html' => '<p>' . $T('A few lines about it.', 'Qualche riga per raccontarla.') . '</p>', 'label' => $T('Read', 'Leggi'), 'href' => 'https://']),
            ]]],
        ['id' => 'b:event', 'name' => $T('Event', 'Evento'), 'description' => $T('Date, place and a booking button', 'Data, luogo e un pulsante per prenotare'),
            'doc' => ['theme' => $theme(['accent' => '#E2553B', 'bg' => '#FBF1EC', 'radius' => 20]), 'blocks' => [
                $b('heading', ['text' => $T('You are invited', 'Sei invitato'), 'level' => 3, 'align' => 'center']),
                $b('heading', ['text' => $T('Name of the event', 'Nome dell’evento'), 'level' => 1, 'align' => 'center']),
                $b('image', ['src' => '', 'alt' => '', 'href' => '', 'width' => 100, 'align' => 'center', 'rounded' => true]),
                $b('text', ['html' => '<p><strong>' . $T('When', 'Quando') . ':</strong> ' . $T('Saturday 12 December, 18:00', 'Sabato 12 dicembre, ore 18:00') . '<br><strong>' . $T('Where', 'Dove') . ':</strong> ' . $T('Address of the venue', 'Indirizzo del luogo') . '</p>', 'align' => 'center']),
                $b('button', ['label' => $T('Book your seat', 'Prenota il tuo posto'), 'href' => 'https://', 'align' => 'center', 'color' => '', 'full' => true]),
                $b('quote', ['text' => $T('A line from a guest or from last year’s edition.', 'Una frase di un ospite o della scorsa edizione.'), 'cite' => '']),
            ]]],
        ['id' => 'b:letter', 'name' => $T('Letter', 'Lettera'), 'description' => $T('Plain text, like a personal message', 'Solo testo, come un messaggio personale'),
            'doc' => ['theme' => $theme(['font' => 'serif', 'accent' => '#8A5A2B', 'bg' => '#F6F1E9', 'card' => '#FFFDF8', 'radius' => 4, 'width' => 560]), 'blocks' => [
                $b('text', ['html' => '<p>' . $T('Dear reader,', 'Caro lettore,') . '</p><p>' . $T('Write your message as you would write a letter.', 'Scrivi il tuo messaggio come scriveresti una lettera.') . '</p><p>' . $T('See you soon,', 'A presto,') . '<br>' . $T('Your name', 'Il tuo nome') . '</p>', 'align' => 'left']),
            ]]],
        ['id' => 'b:welcome', 'name' => $T('Welcome', 'Benvenuto'), 'description' => $T('The first email new subscribers get', 'La prima email per chi si è appena iscritto'),
            'doc' => ['theme' => $theme(['accent' => '#2563EB', 'bg' => '#EEF3FD', 'radius' => 18]), 'blocks' => [
                $b('heading', ['text' => $T('Welcome aboard!', 'Benvenuto a bordo!'), 'level' => 1, 'align' => 'center', 'style' => ['pt' => 16, 'pb' => 4]]),
                $b('text', ['html' => '<p>' . $T('Thank you for subscribing. Here is what you will find in your inbox.', 'Grazie per esserti iscritto. Ecco cosa troverai nella tua casella.') . '</p>', 'align' => 'center', 'style' => ['pb' => 16]]),
                $b('media', ['src' => '', 'alt' => '', 'title' => $T('News first', 'Le novità per primo'), 'html' => '<p>' . $T('What we are working on, before anyone else.', 'Quello a cui lavoriamo, prima di tutti.') . '</p>', 'label' => '', 'href' => '', 'side' => 'left', 'imgWidth' => 30, 'valign' => 'middle', 'rounded' => true, 'gap' => 20]),
                $b('media', ['src' => '', 'alt' => '', 'title' => $T('Tips and stories', 'Consigli e storie'), 'html' => '<p>' . $T('Short reads, once a month.', 'Letture brevi, una volta al mese.') . '</p>', 'label' => '', 'href' => '', 'side' => 'left', 'imgWidth' => 30, 'valign' => 'middle', 'rounded' => true, 'gap' => 20]),
                $b('button', ['label' => $T('Visit our website', 'Visita il nostro sito'), 'href' => 'https://', 'align' => 'center', 'color' => '', 'full' => false, 'style' => ['pt' => 16, 'pb' => 16]]),
            ]]],
        ['id' => 'b:promo', 'name' => $T('Special offer', 'Offerta speciale'), 'description' => $T('A coloured banner, a code and a deadline', 'Un riquadro colorato, un codice e una scadenza'),
            'doc' => ['theme' => $theme(['accent' => '#DB2777', 'bg' => '#FDF0F6', 'radius' => 16]), 'blocks' => [
                $b('heading', ['text' => $T('ONLY THIS WEEK', 'SOLO QUESTA SETTIMANA'), 'level' => 3, 'align' => 'center', 'style' => ['pt' => 20, 'pb' => 0, 'size' => 13, 'color' => '#DB2777']]),
                $b('heading', ['text' => '-20%', 'level' => 1, 'align' => 'center', 'style' => ['pt' => 24, 'pb' => 24, 'size' => 64, 'color' => '#FFFFFF', 'bg' => '#DB2777']]),
                $b('text', ['html' => '<p>' . $T('Use the code <strong>WELCOME20</strong> at checkout.', 'Usa il codice <strong>BENVENUTO20</strong> alla cassa.') . '</p>', 'align' => 'center', 'style' => ['pt' => 20, 'size' => 18]]),
                $b('button', ['label' => $T('Shop now', 'Approfitta ora'), 'href' => 'https://', 'align' => 'center', 'color' => '', 'full' => true, 'style' => ['pt' => 12, 'pb' => 12]]),
                $b('text', ['html' => '<p>' . $T('Valid until Sunday at midnight. Cannot be combined with other offers.', 'Valido fino a domenica a mezzanotte. Non cumulabile con altre offerte.') . '</p>', 'align' => 'center', 'style' => ['size' => 12, 'color' => '#8A7A84']]),
            ]]],
        ['id' => 'b:update', 'name' => $T('Monthly update', 'Aggiornamento mensile'), 'description' => $T('Stories with an image beside the text', 'Notizie con l’immagine accanto al testo'),
            'doc' => ['theme' => $theme(['accent' => '#0F766E', 'bg' => '#ECF5F3', 'font' => 'system']), 'blocks' => [
                $b('heading', ['text' => $T('November update', 'Aggiornamento di novembre'), 'level' => 1, 'align' => 'left']),
                $b('text', ['html' => '<p>' . $T('Three things that happened this month.', 'Tre cose successe questo mese.') . '</p>', 'align' => 'left', 'style' => ['pb' => 16]]),
                $b('media', ['src' => '', 'alt' => '', 'title' => $T('First story', 'Prima notizia'), 'html' => '<p>' . $T('A few lines about it.', 'Qualche riga per raccontarla.') . '</p>', 'label' => $T('Read', 'Leggi'), 'href' => 'https://', 'side' => 'left', 'imgWidth' => 40, 'valign' => 'top', 'rounded' => true, 'gap' => 20, 'style' => ['pb' => 16]]),
                $b('media', ['src' => '', 'alt' => '', 'title' => $T('Second story', 'Seconda notizia'), 'html' => '<p>' . $T('A few lines about it.', 'Qualche riga per raccontarla.') . '</p>', 'label' => $T('Read', 'Leggi'), 'href' => 'https://', 'side' => 'right', 'imgWidth' => 40, 'valign' => 'top', 'rounded' => true, 'gap' => 20, 'style' => ['pb' => 16]]),
                $b('media', ['src' => '', 'alt' => '', 'title' => $T('Third story', 'Terza notizia'), 'html' => '<p>' . $T('A few lines about it.', 'Qualche riga per raccontarla.') . '</p>', 'label' => $T('Read', 'Leggi'), 'href' => 'https://', 'side' => 'left', 'imgWidth' => 40, 'valign' => 'top', 'rounded' => true, 'gap' => 20]),
                $b('divider', ['color' => '', 'thickness' => 1]),
                $b('social', ['links' => [], 'align' => 'center']),
            ]]],
        ['id' => 'b:links', 'name' => $T('Link roundup', 'Raccolta di link'), 'description' => $T('A list of things worth reading', 'Un elenco di cose da leggere'),
            'doc' => ['theme' => $theme(['accent' => '#D97706', 'bg' => '#FBF6EC', 'font' => 'verdana', 'radius' => 8]), 'blocks' => [
                $b('heading', ['text' => $T('5 links for your weekend', '5 link per il tuo fine settimana'), 'level' => 2, 'align' => 'left']),
                $b('text', ['html' => '<ol><li><a href="https://">' . $T('Title of the first article', 'Titolo del primo articolo') . '</a> — ' . $T('why it is worth it', 'perché vale la pena') . '</li><li><a href="https://">' . $T('Second article', 'Secondo articolo') . '</a> — ' . $T('one line', 'una riga') . '</li><li><a href="https://">' . $T('Third article', 'Terzo articolo') . '</a> — ' . $T('one line', 'una riga') . '</li><li><a href="https://">' . $T('Fourth article', 'Quarto articolo') . '</a> — ' . $T('one line', 'una riga') . '</li><li><a href="https://">' . $T('Fifth article', 'Quinto articolo') . '</a> — ' . $T('one line', 'una riga') . '</li></ol>', 'align' => 'left', 'style' => ['lh' => 1.8]]),
                $b('quote', ['text' => $T('The quote of the week.', 'La citazione della settimana.'), 'cite' => '', 'style' => ['pt' => 16, 'pb' => 16]]),
            ]]],
        ['id' => 'b:survey', 'name' => $T('Feedback', 'Sondaggio'), 'description' => $T('Ask your readers a question', 'Fai una domanda ai tuoi lettori'),
            'doc' => ['theme' => $theme(['accent' => '#7C3AED', 'bg' => '#F4F0FE', 'radius' => 20, 'width' => 560]), 'blocks' => [
                $b('heading', ['text' => $T('How are we doing?', 'Come stiamo andando?'), 'level' => 1, 'align' => 'center', 'style' => ['pt' => 16]]),
                $b('text', ['html' => '<p>' . $T('Two minutes of your time help us write a better newsletter.', 'Due minuti del tuo tempo ci aiutano a scrivere una newsletter migliore.') . '</p>', 'align' => 'center', 'style' => ['pb' => 16]]),
                $b('button', ['label' => $T('Answer the survey', 'Rispondi al sondaggio'), 'href' => 'https://', 'align' => 'center', 'color' => '', 'full' => false]),
                $b('text', ['html' => '<p>' . $T('Thank you!', 'Grazie!') . '</p>', 'align' => 'center', 'style' => ['pt' => 16, 'pb' => 16, 'size' => 14]]),
            ]]],
        ['id' => 'b:greetings', 'name' => $T('Greetings', 'Auguri'), 'description' => $T('Holidays and special occasions', 'Feste e occasioni speciali'),
            'doc' => ['theme' => $theme(['font' => 'garamond', 'accent' => '#B91C1C', 'bg' => '#F5EFE6', 'card' => '#FFFCF7', 'radius' => 6, 'width' => 560]), 'blocks' => [
                $b('image', ['src' => '', 'alt' => '', 'href' => '', 'width' => 100, 'align' => 'center', 'rounded' => true, 'style' => ['pt' => 0, 'px' => 0]]),
                $b('heading', ['text' => $T('Happy holidays', 'Buone feste'), 'level' => 1, 'align' => 'center', 'style' => ['pt' => 24, 'size' => 38, 'weight' => '400']]),
                $b('text', ['html' => '<p>' . $T('Thank you for being with us this year. We wish you and your family peaceful days.', 'Grazie per essere stato con noi quest’anno. Auguriamo a te e alla tua famiglia giorni sereni.') . '</p>', 'align' => 'center', 'style' => ['size' => 18]]),
                $b('divider', ['color' => '#B91C1C', 'thickness' => 2, 'style' => ['px' => 64]]),
                $b('text', ['html' => '<p>' . $T('The whole team', 'Tutto il team') . '</p>', 'align' => 'center', 'style' => ['pb' => 16, 'size' => 16]]),
            ]]],
    ];
}

function builtin_template(string $id, string $lang): ?array
{
    foreach (builtin_templates($lang) as $t) if ($t['id'] === $id) return $t;
    return null;
}
