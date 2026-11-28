<?php
/** Algorithms and data structures: Big O complexity, arrays, hash maps, stacks and queues, searching and sorting, recursion, trees and graphs,
 *  dynamic programming and greedy, binary search trees, heaps, two pointers and sliding windows, Dijkstra. */

/** Big O growth curves on the same axes, clipped to the chart: how the steps grow with n. */
if (!function_exists('fig_algo_bigo')) {
    function fig_algo_bigo(string $xl, string $yl): string
    {
        $fx = fn($n) => 70 + ($n - 1) * (450 / 31);
        $fy = fn($v) => 330 - min($v, 80) * (300 / 64);
        $curves = [
            ['O(1)', fn($n) => 1, '#0B7F5C', ''], ['O(log n)', fn($n) => log($n, 2) + 1, '#2F8CF0', ''],
            ['O(n)', fn($n) => $n, '#7048E8', ''], ['O(n log n)', fn($n) => $n * log(max($n, 1), 2), '#E8590C', ' stroke-dasharray="8 4"'],
            ['O(n²)', fn($n) => $n * $n, '#F03E82', ' stroke-dasharray="3 3"'], ['O(2ⁿ)', fn($n) => 2 ** $n, '#1E1C52', ' stroke-dasharray="10 3 2 3"'],
        ];
        $out = '';
        foreach ($curves as [$label, $f, $col, $dash]) {
            $pts = []; $last = null;
            for ($n = 1; $n <= 32; $n += 0.25) { $v = $f($n); $pts[] = round($fx($n), 1) . ',' . round($fy($v), 1); $last = [$n, $v]; if ($v > 70) break; }
            $out .= "<polyline points=\"" . implode(' ', $pts) . "\" fill=\"none\" stroke=\"$col\" stroke-width=\"3\"$dash clip-path=\"url(#c)\"/>";
            [$n, $v] = $last;
            $end = $label === 'O(2ⁿ)';
            $lx = $v > 64 ? round($fx($n)) + ($end ? -6 : 6) : 528; $ly = $v > 64 ? 46 : round($fy($v)) + 5;
            $out .= "<text x=\"$lx\" y=\"$ly\" font-size=\"13\" font-weight=\"700\" fill=\"$col\"" . ($end ? ' text-anchor="end"' : '') . ">$label</text>";
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 370" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><clipPath id="c"><rect x="70" y="30" width="452" height="302"/></clipPath></defs>
<g stroke="#D9D7E8" stroke-width="1"><line x1="70" y1="255" x2="520" y2="255"/><line x1="70" y1="180" x2="520" y2="180"/><line x1="70" y1="105" x2="520" y2="105"/></g>
<path d="M70,24 V330 H524" fill="none" stroke="#1E1C52" stroke-width="2"/>
{$out}
<text x="520" y="356" text-anchor="end" font-size="14">{$xl} →</text>
<text x="20" y="190" font-size="14" transform="rotate(-90 20 190)" text-anchor="middle">{$yl} →</text>
</svg>
SVG;
    }
}

/** A stack and a queue side by side: the stack adds and removes at the top, the queue adds at the back and removes at the front. */
if (!function_exists('fig_algo_stackqueue')) {
    function fig_algo_stackqueue(array $t): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<defs><marker id="ah" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="6" markerHeight="6" orient="auto"><path d="M0,0 L10,5 L0,10 z" fill="#1E1C52"/></marker></defs>
<rect x="8" y="8" width="234" height="254" rx="12" fill="#F1F0F8"/><rect x="254" y="8" width="378" height="254" rx="12" fill="#F1F0F8"/>
<text x="24" y="36" font-weight="700">{$t[0]}</text><text x="270" y="36" font-weight="700">{$t[1]}</text>
<path d="M60,90 V236 H160 V90" fill="none" stroke="#8A87B0" stroke-width="2"/>
<g stroke-width="2" font-family="ui-monospace, Menlo, monospace" font-weight="700" text-anchor="middle">
<rect x="66" y="192" width="88" height="38" rx="8" fill="#DCEBFF" stroke="#2F8CF0"/><text x="110" y="216">1</text>
<rect x="66" y="148" width="88" height="38" rx="8" fill="#DCEBFF" stroke="#2F8CF0"/><text x="110" y="172">2</text>
<rect x="66" y="104" width="88" height="38" rx="8" fill="#FFE8D6" stroke="#E8590C"/><text x="110" y="128">3</text>
</g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#ah)"><path d="M74,68 C74,80 86,86 92,98"/><path d="M128,98 C134,86 146,80 146,68"/></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="13" text-anchor="middle"><text x="66" y="62">append</text><text x="160" y="62">pop</text></g>
<text x="200" y="128" font-size="13" text-anchor="middle">{$t[2]}</text>
<path d="M330,120 H560 M330,176 H560" stroke="#8A87B0" stroke-width="2"/>
<g stroke-width="2" font-family="ui-monospace, Menlo, monospace" font-weight="700" text-anchor="middle">
<rect x="344" y="128" width="62" height="40" rx="8" fill="#FFE8D6" stroke="#E8590C"/><text x="375" y="153">"a"</text>
<rect x="414" y="128" width="62" height="40" rx="8" fill="#DCEBFF" stroke="#2F8CF0"/><text x="445" y="153">"b"</text>
<rect x="484" y="128" width="62" height="40" rx="8" fill="#DCEBFF" stroke="#2F8CF0"/><text x="515" y="153">"c"</text>
</g>
<g stroke="#1E1C52" stroke-width="2" fill="none" marker-end="url(#ah)"><path d="M612,148 H556"/><path d="M340,148 H276"/></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="13" text-anchor="middle"><text x="586" y="212">append</text><text x="304" y="212">popleft</text></g>
<g font-size="13" text-anchor="middle"><text x="586" y="232">{$t[3]}</text><text x="304" y="232">{$t[4]}</text></g>
</svg>
SVG;
    }
}

/** Binary search for 37 in a sorted list: at each step the middle is compared and half of the range is discarded. */
if (!function_exists('fig_algo_binary')) {
    function fig_algo_binary(string $step, string $target): string
    {
        $vals = [3, 8, 12, 17, 21, 25, 30, 37, 42, 48, 55];
        $steps = [[0, 10, 5], [6, 10, 8], [6, 7, 6], [7, 7, 7]];
        $out = '';
        foreach ($steps as $r => [$lo, $hi, $mid]) {
            $y = 50 + $r * 52;
            $out .= "<text x=\"16\" y=\"" . ($y + 26) . "\" font-family=\"system-ui, sans-serif\" font-size=\"14\" font-weight=\"700\">$step " . ($r + 1) . "</text>";
            foreach ($vals as $i => $v) {
                $x = 112 + $i * 46;
                if ($i === $mid) [$f, $s, $d, $c] = $v === 37 ? ['#D5F6EA', '#0B7F5C', '', '#0B7F5C'] : ['#FFE8D6', '#E8590C', '', '#B84508'];
                elseif ($i >= $lo && $i <= $hi) [$f, $s, $d, $c] = ['#DCEBFF', '#2F8CF0', '', '#1E1C52'];
                else [$f, $s, $d, $c] = ['#FFFFFF', '#D9D7E8', ' stroke-dasharray="4 3"', '#B5B3CC'];
                $w = $i === $mid ? '700' : '400';
                $out .= "<rect x=\"$x\" y=\"$y\" width=\"42\" height=\"38\" rx=\"7\" fill=\"$f\" stroke=\"$s\" stroke-width=\"2\"$d/><text x=\"" . ($x + 21) . "\" y=\"" . ($y + 25) . "\" text-anchor=\"middle\" font-weight=\"$w\" fill=\"$c\">$v</text>";
            }
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="ui-monospace, Menlo, monospace" font-size="15" fill="#1E1C52">
<text x="112" y="32" font-family="system-ui, sans-serif" font-size="14">{$target}: <tspan font-weight="700" font-family="ui-monospace, Menlo, monospace">37</tspan></text>
{$out}
</svg>
SVG;
    }
}

/** Bubble sort on five bars: neighbours that are out of order swap places until the bars are sorted. Still, it shows the sorted result. */
if (!function_exists('fig_algo_bubble')) {
    function fig_algo_bubble(): string
    {
        $vals = [5, 1, 4, 2, 3];
        $order = [0, 1, 2, 3, 4]; $frames = [$order]; $a = $vals;
        for ($i = 0; $i < 5; $i++) for ($j = 0; $j < 4 - $i; $j++) if ($a[$j] > $a[$j + 1]) {
            [$a[$j], $a[$j + 1]] = [$a[$j + 1], $a[$j]]; [$order[$j], $order[$j + 1]] = [$order[$j + 1], $order[$j]]; $frames[] = $order;
        }
        $n = count($frames); $total = $n + 2; $css = ''; $bars = '';
        foreach ($vals as $b => $v) {
            $kf = '';
            foreach ($frames as $k => $fr) {
                $dx = (array_search($b, $fr) - $b) * 100;
                $p1 = round($k / $total * 100, 1); $p2 = round(($k + 0.6) / $total * 100, 1);
                $kf .= "{$p1}%,{$p2}%{transform:translateX({$dx}px)}";
            }
            $final = (array_search($b, end($frames)) - $b) * 100;
            $kf .= "100%{transform:translateX({$final}px)}";
            $css .= ".b$b{transform:translateX({$final}px);animation:k$b {$total}s ease-in-out infinite}@keyframes k$b{0%{transform:translateX(0)}$kf}";
            $h = $v * 44; $x = 90 + $b * 100; $y = 250 - $h;
            [$f, $s] = [['#FFE8D6', '#E8590C'], ['#DCEBFF', '#2F8CF0'], ['#D5F6EA', '#0B7F5C'], ['#ECE6FF', '#7048E8'], ['#FFE3EE', '#F03E82']][$v - 1];
            $bars .= "<g class=\"b$b\"><rect x=\"$x\" y=\"$y\" width=\"60\" height=\"$h\" rx=\"8\" fill=\"$f\" stroke=\"$s\" stroke-width=\"2\"/><text x=\"" . ($x + 30) . "\" y=\"" . ($y - 8) . "\" text-anchor=\"middle\" font-weight=\"700\">$v</text></g>";
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="ui-monospace, Menlo, monospace" font-size="16" fill="#1E1C52">
<style>{$css}@media (prefers-reduced-motion:reduce){.b0,.b1,.b2,.b3,.b4{animation:none}}</style>
<line x1="70" y1="252" x2="570" y2="252" stroke="#8A87B0" stroke-width="2"/>
{$bars}
</svg>
SVG;
    }
}

/** The call tree of fib(5): with memoisation the repeated calls are answered from the cache and their subtrees disappear. */
if (!function_exists('fig_algo_fib')) {
    function fig_algo_fib(string $computed, string $cached): string
    {
        $nodes = [['f(5)', 320, 40, 1], ['f(4)', 210, 110, 1], ['f(3)', 470, 110, 0], ['f(3)', 130, 180, 1], ['f(2)', 300, 180, 0], ['f(2)', 80, 250, 1], ['f(1)', 200, 250, 1], ['f(1)', 36, 320, 1], ['f(0)', 124, 320, 1]];
        $edges = [[0, 1], [0, 2], [1, 3], [1, 4], [3, 5], [3, 6], [5, 7], [5, 8]];
        $out = '';
        foreach ($edges as [$a, $b]) $out .= "<line x1=\"{$nodes[$a][1]}\" y1=\"" . ($nodes[$a][2] + 16) . "\" x2=\"{$nodes[$b][1]}\" y2=\"" . ($nodes[$b][2] - 16) . "\" stroke=\"#8A87B0\" stroke-width=\"2\"/>";
        foreach ($nodes as [$l, $x, $y, $c]) {
            $x0 = max(4, $x - 32);
            $out .= $c
                ? "<rect x=\"$x0\" y=\"" . ($y - 16) . "\" width=\"64\" height=\"32\" rx=\"16\" fill=\"#DCEBFF\" stroke=\"#2F8CF0\" stroke-width=\"2\"/>"
                : "<rect x=\"$x0\" y=\"" . ($y - 16) . "\" width=\"64\" height=\"32\" rx=\"16\" fill=\"#D5F6EA\" stroke=\"#0B7F5C\" stroke-width=\"2\" stroke-dasharray=\"5 3\"/>";
            $out .= "<text x=\"" . ($x0 + 32) . "\" y=\"" . ($y + 5) . "\" text-anchor=\"middle\" font-weight=\"700\">$l</text>";
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 350" font-family="ui-monospace, Menlo, monospace" font-size="14" fill="#1E1C52">
{$out}
<g font-family="system-ui, sans-serif" font-size="13">
<rect x="380" y="232" width="40" height="22" rx="11" fill="#DCEBFF" stroke="#2F8CF0" stroke-width="2"/><text x="430" y="248">{$computed}</text>
<rect x="380" y="270" width="40" height="22" rx="11" fill="#D5F6EA" stroke="#0B7F5C" stroke-width="2" stroke-dasharray="5 3"/><text x="430" y="286">{$cached}</text>
</g>
</svg>
SVG;
    }
}

/** A balanced binary search tree with the search path for 40, beside the list-like tree you get by inserting sorted values. */
if (!function_exists('fig_algo_bst')) {
    function fig_algo_bst(array $t): string
    {
        $node = function (int $x, int $y, int $v, int $on): string {
            [$f, $s] = $on ? ['#FFE8D6', '#E8590C'] : ['#DCEBFF', '#2F8CF0'];
            return "<circle cx=\"$x\" cy=\"$y\" r=\"20\" fill=\"$f\" stroke=\"$s\" stroke-width=\"" . ($on ? 3 : 2) . "\"/><text x=\"$x\" y=\"" . ($y + 5) . "\" text-anchor=\"middle\" font-weight=\"700\">$v</text>";
        };
        $l = fn($x1, $y1, $x2, $y2, $on) => "<line x1=\"$x1\" y1=\"$y1\" x2=\"$x2\" y2=\"$y2\" stroke=\"" . ($on ? '#E8590C' : '#8A87B0') . "\" stroke-width=\"" . ($on ? 3 : 2) . "\"/>";
        $left = $l(160, 74, 100, 136, 1) . $l(160, 74, 220, 136, 0) . $l(100, 156, 60, 216, 0) . $l(100, 156, 140, 216, 1)
              . $node(160, 64, 50, 1) . $node(100, 146, 30, 1) . $node(220, 146, 70, 0) . $node(60, 226, 20, 0) . $node(140, 226, 40, 1);
        $right = '';
        foreach ([20, 30, 40, 50, 70] as $i => $v) { $x = 380 + $i * 52; $y = 64 + $i * 44; if ($i) $right .= $l($x - 52 + 14, $y - 44 + 14, $x - 14, $y - 14, 0); }
        foreach ([20, 30, 40, 50, 70] as $i => $v) $right .= $node(380 + $i * 52, 64 + $i * 44, $v, 0);
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 310" font-family="ui-monospace, Menlo, monospace" font-size="14" fill="#1E1C52">
<line x1="320" y1="16" x2="320" y2="294" stroke="#D9D7E8" stroke-width="2"/>
{$left}{$right}
<g font-family="system-ui, sans-serif" font-size="13">
<text x="160" y="270" text-anchor="middle" font-weight="700">{$t[0]}</text><text x="160" y="290" text-anchor="middle">{$t[1]}</text>
<text x="380" y="270" font-weight="700">{$t[2]}</text><text x="380" y="290">{$t[3]}</text>
<text x="200" y="40" fill="#B84508" font-weight="700">{$t[4]}</text>
</g>
</svg>
SVG;
    }
}

/** A min-heap drawn as a tree and as the list that stores it: the children of position i are at 2i+1 and 2i+2. */
if (!function_exists('fig_algo_heap')) {
    function fig_algo_heap(string $note): string
    {
        $vals = [1, 3, 6, 7, 8, 9];
        $pos = [[160, 50], [90, 130], [230, 130], [50, 210], [130, 210], [190, 210]];
        $out = '';
        foreach ([[0, 1], [0, 2], [1, 3], [1, 4], [2, 5]] as [$a, $b]) $out .= "<line x1=\"{$pos[$a][0]}\" y1=\"" . ($pos[$a][1] + 20) . "\" x2=\"{$pos[$b][0]}\" y2=\"" . ($pos[$b][1] - 20) . "\" stroke=\"#8A87B0\" stroke-width=\"2\"/>";
        foreach ($vals as $i => $v) {
            [$x, $y] = $pos[$i]; [$f, $s] = $i === 0 ? ['#FFE8D6', '#E8590C'] : ['#DCEBFF', '#2F8CF0'];
            $out .= "<circle cx=\"$x\" cy=\"$y\" r=\"20\" fill=\"$f\" stroke=\"$s\" stroke-width=\"2\"/><text x=\"$x\" y=\"" . ($y + 5) . "\" text-anchor=\"middle\" font-weight=\"700\">$v</text><text x=\"" . ($x + 24) . "\" y=\"" . ($y - 14) . "\" font-size=\"13\" fill=\"#6E6B96\">$i</text>";
            $cx = 330 + $i * 48;
            $out .= "<rect x=\"$cx\" y=\"110\" width=\"44\" height=\"40\" rx=\"7\" fill=\"$f\" stroke=\"$s\" stroke-width=\"2\"/><text x=\"" . ($cx + 22) . "\" y=\"136\" text-anchor=\"middle\" font-weight=\"700\">$v</text><text x=\"" . ($cx + 22) . "\" y=\"170\" text-anchor=\"middle\" font-size=\"13\" fill=\"#6E6B96\">$i</text>";
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 260" font-family="ui-monospace, Menlo, monospace" font-size="15" fill="#1E1C52">
{$out}
<g fill="none" stroke="#7048E8" stroke-width="2"><path d="M352,108 C360,78 392,78 400,108"/><path d="M352,108 C366,56 434,56 448,108"/></g>
<text x="470" y="64" font-size="13" fill="#5A35C9" font-weight="700">2i+1 · 2i+2</text>
<text x="330" y="212" font-family="system-ui, sans-serif" font-size="13">{$note}</text>
</svg>
SVG;
    }
}

/** A sliding window of three values moving along the list: each step adds the value that enters and removes the one that leaves. */
if (!function_exists('fig_algo_window')) {
    function fig_algo_window(string $sum, string $best): string
    {
        $vals = [2, 1, 5, 1, 3, 2]; $cells = '';
        foreach ($vals as $i => $v) { $x = 110 + $i * 72; $cells .= "<rect x=\"$x\" y=\"90\" width=\"62\" height=\"56\" rx=\"10\" fill=\"#FFFFFF\" stroke=\"#8A87B0\" stroke-width=\"2\"/><text x=\"" . ($x + 31) . "\" y=\"125\" text-anchor=\"middle\" font-size=\"18\" font-weight=\"700\">$v</text>"; }
        $sums = [8, 7, 9, 6]; $labels = '';
        foreach ($sums as $k => $s) {
            $cls = "s$k"; $x = 104 + $k * 72 + 105;
            $labels .= "<text class=\"$cls\" x=\"$x\" y=\"190\" text-anchor=\"middle\" font-family=\"system-ui, sans-serif\" font-size=\"15\" font-weight=\"700\"" . ($s === 9 ? ' fill="#0B7F5C"' : '') . ">$sum = $s" . ($s === 9 ? " · $best" : '') . "</text>";
        }
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 230" font-family="ui-monospace, Menlo, monospace" font-size="15" fill="#1E1C52">
<style>.win{transform:translateX(144px);animation:slide 8s steps(1) infinite}.s0,.s1,.s3{opacity:0}.s0{animation:o0 8s steps(1) infinite}.s1{animation:o1 8s steps(1) infinite}.s2{animation:o2 8s steps(1) infinite}.s3{animation:o3 8s steps(1) infinite}
@keyframes slide{0%{transform:translateX(0)}25%{transform:translateX(72px)}50%{transform:translateX(144px)}75%{transform:translateX(216px)}}
@keyframes o0{0%{opacity:1}25%,100%{opacity:0}}@keyframes o1{0%{opacity:0}25%{opacity:1}50%,100%{opacity:0}}@keyframes o2{0%,25%{opacity:0}50%{opacity:1}75%,100%{opacity:0}}@keyframes o3{0%,50%{opacity:0}75%{opacity:1}}
@media (prefers-reduced-motion:reduce){.win,.s0,.s1,.s2,.s3{animation:none}}</style>
{$cells}
<g class="win"><rect x="104" y="80" width="222" height="76" rx="14" fill="none" stroke="#E8590C" stroke-width="3"/><text x="215" y="66" text-anchor="middle" font-size="14" font-weight="700" fill="#B84508">k = 3</text></g>
{$labels}
</svg>
SVG;
    }
}

/** Dijkstra on three cities: the direct road to Venezia (130) loses to the one through Pordenone (50 + 70 = 120). */
if (!function_exists('fig_algo_dijkstra')) {
    function fig_algo_dijkstra(): string
    {
        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 270" font-family="system-ui, sans-serif" font-size="15" fill="#1E1C52">
<path d="M150,190 L320,70" stroke="#E8590C" stroke-width="4"/><path d="M320,70 L490,190" stroke="#E8590C" stroke-width="4"/>
<path d="M150,210 H490" stroke="#8A87B0" stroke-width="2" stroke-dasharray="7 5"/>
<g font-family="ui-monospace, Menlo, monospace" font-weight="700" font-size="15">
<rect x="208" y="108" width="40" height="26" rx="8" fill="#FFFFFF" stroke="#E8590C" stroke-width="2"/><text x="228" y="126" text-anchor="middle" fill="#B84508">50</text>
<rect x="392" y="108" width="40" height="26" rx="8" fill="#FFFFFF" stroke="#E8590C" stroke-width="2"/><text x="412" y="126" text-anchor="middle" fill="#B84508">70</text>
<rect x="298" y="197" width="44" height="26" rx="8" fill="#FFFFFF" stroke="#8A87B0" stroke-width="2"/><text x="320" y="215" text-anchor="middle" fill="#6E6B96">130</text>
</g>
<g stroke-width="2"><rect x="60" y="176" width="120" height="48" rx="24" fill="#DCEBFF" stroke="#2F8CF0"/><rect x="250" y="30" width="140" height="48" rx="24" fill="#DCEBFF" stroke="#2F8CF0"/><rect x="460" y="176" width="120" height="48" rx="24" fill="#D5F6EA" stroke="#0B7F5C"/></g>
<g font-weight="700" text-anchor="middle"><text x="120" y="205">Udine</text><text x="320" y="59">Pordenone</text><text x="520" y="205">Venezia</text></g>
<g font-family="ui-monospace, Menlo, monospace" font-size="14" text-anchor="middle">
<text x="120" y="250">d = 0</text><text x="430" y="40" text-anchor="start">d = 50</text>
<text x="520" y="250"><tspan font-weight="700" fill="#0B7F5C">d = 120</tspan> <tspan fill="#6E6B96" text-decoration="line-through">130</tspan></text>
</g>
</svg>
SVG;
    }
}


return [
    'id' => 'algorithms', 'family' => 'algorithms', 'after' => 'python-base',
    'title' => L('Algoritmi e strutture dati', 'Algorithms and Data Structures'), 'short' => L('Algoritmi', 'Algorithms'), 'level' => L('Intermedio', 'Intermediate'),
    'desc' => L('Pensare in modo efficiente: complessità O grande, array e mappe, pile e code, ricerca e ordinamento, ricorsione, alberi, heap e grafi, programmazione dinamica, due puntatori e cammini minimi. Esempi in Python.', 'Thinking efficiently: Big O complexity, arrays and maps, stacks and queues, searching and sorting, recursion, trees, heaps and graphs, dynamic programming, two pointers and shortest paths. Examples in Python.'),
    'lessons' => [
        lesson('bigo', L('Complessità: la notazione O', 'Complexity: Big O notation'), [
            P('La **notazione O grande** descrive come cresce il tempo (o la memoria) di un algoritmo quando cresce l’input `n`. Non conta i secondi esatti, ma l’andamento: raddoppiando i dati, il tempo resta uguale, raddoppia o esplode?', '**Big O notation** describes how an algorithm’s time (or memory) grows as the input `n` grows. It does not count exact seconds, but the trend: doubling the data, does time stay the same, double or explode?'),
            C('O(1)        costante      leggere lista[5], cercare in un dizionario
O(log n)    logaritmica   ricerca binaria in una lista ordinata
O(n)        lineare       scorrere tutta la lista una volta
O(n log n)  quasi lineare buoni algoritmi di ordinamento
O(n²)       quadratica    due cicli annidati sugli stessi dati
O(2ⁿ)       esponenziale  provare tutte le combinazioni', 'text'),
            P('Con un milione di elementi, O(n²) significa mille miliardi di passi: la differenza tra un millisecondo e un’ora. Spesso basta scegliere la struttura dati giusta per passare da O(n²) a O(n).', 'With a million items, O(n²) means a trillion steps: the difference between a millisecond and an hour. Often just picking the right data structure takes you from O(n²) to O(n).'),
            Fig(L(fig_algo_bigo('n (quantità di dati)', 'passi'), fig_algo_bigo('n (amount of data)', 'steps')),
                L('Un grafico con n sull’asse orizzontale e i passi su quello verticale. O(1) è una linea piatta, O(log n) cresce appena, O(n) è una retta, O(n log n) sale un po’ più in fretta, O(n²) e O(2ⁿ) schizzano verso l’alto quasi subito.', 'A chart with n on the horizontal axis and steps on the vertical one. O(1) is a flat line, O(log n) barely grows, O(n) is a straight line, O(n log n) climbs a little faster, O(n²) and O(2ⁿ) shoot upwards almost at once.'),
                L('Con pochi dati le curve sono vicine; è quando n cresce che la scelta conta.', 'With little data the curves are close; it is when n grows that the choice matters.')),
            Tip('Per stimare la complessità guarda i cicli: uno che scorre i dati è O(n), uno dentro l’altro O(n²), uno che dimezza a ogni giro O(log n). Costanti e termini minori si buttano: O(2n + 5) è O(n). E per n piccoli un algoritmo «peggiore» può essere più veloce nella pratica.', 'To estimate complexity look at the loops: one that goes through the data is O(n), one inside another O(n²), one that halves at every turn O(log n). Constants and smaller terms are dropped: O(2n + 5) is O(n). And for small n a “worse” algorithm can be faster in practice.'),
        ], [
            Q('o1', L('Che complessità hanno due cicli annidati sugli stessi n dati?', 'What complexity do two nested loops over the same n items have?'), ['O(n²)', 'O(n)', 'O(1)', 'O(log n)'], 0, L('n volte n.', 'n times n.')),
            Q('o2', L('Che complessità ha la ricerca binaria?', 'What complexity does binary search have?'), ['O(log n)', 'O(n)', 'O(n²)', 'O(2ⁿ)'], 0, L('Dimezza a ogni passo.', 'It halves each step.')),
            Q('o3', L('Che cosa descrive la notazione O?', 'What does Big O describe?'), [L('Come cresce il costo al crescere dell’input', 'How cost grows as input grows'), L('I secondi esatti', 'Exact seconds'), L('Il linguaggio usato', 'The language used'), L('La memoria del PC', 'The PC’s memory')], 0, L('L’andamento, non il tempo preciso.', 'The trend, not precise time.')),
        ], task('text', L('Indica la complessità di ciascuna operazione, una per riga come `operazione: O(...)`: `leggere un elemento per indice`, `sommare tutti gli elementi`, `confrontare ogni elemento con ogni altro`.', 'Give the complexity of each operation, one per line as `operation: O(...)`: `read an element by index`, `sum all elements`, `compare every element with every other`.'), '', [re('(indice|index)[^:\n]*:\s*O\(1\)', L('Per indice: O(1)', 'By index: O(1)')), re('(somm|sum)[^:\n]*:\s*O\(n\)', L('Somma: O(n)', 'Sum: O(n)')), re('(confront|compar)[^:\n]*:\s*O\(n(²|\^2|\*n)\)', L('Tutti con tutti: O(n²)', 'All with all: O(n²)'))], '', "leggere un elemento per indice: O(1)\nsommare tutti gli elementi: O(n)\nconfrontare ogni elemento con ogni altro: O(n²)"), [
            dOrder(pSort(), ['O(1)', 'O(log n)', 'O(n)', 'O(n log n)', 'O(n²)'], L('Dalla più veloce alla più lenta.', 'From fastest to slowest.')),
            dFlash(pFlash(), [[L('O(log n) cresce più lentamente di O(n)', 'O(log n) grows more slowly than O(n)'), true], [L('O(n²) è sempre veloce con milioni di dati', 'O(n²) is always fast with millions of items'), false], [L('Leggere da un dizionario è in media O(1)', 'Reading from a dict is O(1) on average'), true], [L('La notazione O misura i secondi esatti', 'Big O measures exact seconds'), false]], L('Pensare in grande.', 'Thinking big.')),
        ]),
        lesson('structures', L('Strutture dati fondamentali', 'Core data structures'), [
            P('Le strutture dati sono il modo di organizzare le informazioni. Le più usate: **array/lista** (accesso per indice O(1), ricerca O(n)), **hash map/dizionario** (ricerca per chiave in media O(1)), **set** (appartenenza in O(1)), **pila** (l’ultimo entrato esce per primo, LIFO) e **coda** (il primo entrato esce per primo, FIFO).', 'Data structures are how information is organised. The most used: **array/list** (index access O(1), search O(n)), **hash map/dictionary** (key lookup O(1) on average), **set** (membership O(1)), **stack** (last in, first out, LIFO) and **queue** (first in, first out, FIFO).'),
            C('# trovare doppioni: O(n²) con due cicli, O(n) con un set
def ha_doppioni(lista):
    visti = set()
    for x in lista:
        if x in visti:
            return True
        visti.add(x)
    return False

from collections import deque
coda = deque(); coda.append("a"); coda.popleft()   # FIFO
pila = []; pila.append(1); pila.pop()              # LIFO', 'python'),
            P('Una pila serve per “annulla”, parentesi bilanciate e chiamate di funzione; una coda per elaborare richieste nell’ordine di arrivo. Il trucco del set qui sopra è uno dei più utili: trasforma ricerche lente in istantanee.', 'A stack is used for “undo”, balanced brackets and function calls; a queue for processing requests in arrival order. The set trick above is one of the most useful: it turns slow lookups into instant ones.'),
            Fig(L(fig_algo_stackqueue(['Pila (LIFO)', 'Coda (FIFO)', 'esce il 3', 'entra in fondo', 'esce il primo']), fig_algo_stackqueue(['Stack (LIFO)', 'Queue (FIFO)', '3 leaves', 'joins at the back', 'the first leaves'])),
                L('A sinistra una pila verticale con 1, 2 e 3: append mette in cima e pop toglie dalla cima, quindi esce il 3, l’ultimo entrato. A destra una coda orizzontale con "a", "b" e "c": append aggiunge in fondo a destra, popleft toglie "a" dal davanti a sinistra.', 'On the left a vertical stack with 1, 2 and 3: append puts on top and pop takes from the top, so 3 leaves, the last one in. On the right a horizontal queue with "a", "b" and "c": append adds at the back on the right, popleft takes "a" from the front on the left.'),
                L('Stessa operazione per entrare, uscita da lati opposti.', 'The same operation to get in, the exit on opposite sides.')),
        ], [
            Q('s1', L('Come funziona una pila?', 'How does a stack work?'), [L('L’ultimo entrato esce per primo', 'Last in, first out'), L('Il primo entrato esce per primo', 'First in, first out'), L('A caso', 'At random'), L('Per ordine alfabetico', 'Alphabetically')], 0, L('LIFO, come una pila di piatti.', 'LIFO, like a stack of plates.')),
            Q('s2', L('Quanto costa in media `x in set`?', 'What does `x in set` cost on average?'), ['O(1)', 'O(n)', 'O(n²)', 'O(log n)'], 0, L('Hash.', 'Hashing.')),
            Q('s3', L('Quale struttura per servire richieste nell’ordine di arrivo?', 'Which structure serves requests in arrival order?'), [L('Una coda', 'A queue'), L('Una pila', 'A stack'), L('Un set', 'A set'), L('Un albero', 'A tree')], 0, L('FIFO.', 'FIFO.')),
        ], task('python', L('Scrivi `conta_parole(testo)` che restituisce un dizionario parola → numero di volte, usando un `dict` e `testo.split()`.', 'Write `conta_parole(testo)` returning a dict word → count, using a `dict` and `testo.split()`.'), '', [re('def\s+conta_parole\s*\(\s*testo\s*\)', L('La funzione', 'The function')), re('for\s+(\w+)\s+in\s+testo\.split\s*\(\s*\)', L('Scorri le parole', 'You loop over the words')), re('\[\s*\w+\s*\]\s*=\s*\w+\.get\s*\(\s*\w+\s*,\s*0\s*\)\s*\+\s*1|\[\s*\w+\s*\]\s*\+=\s*1', L('Conti con il dizionario', 'You count with the dict')), re('return\s+\w+', L('Restituisci il risultato', 'You return the result'))], '', "def conta_parole(testo):\n    conteggi = {}\n    for p in testo.split():\n        conteggi[p] = conteggi.get(p, 0) + 1\n    return conteggi"), [
            dMatch(pMatch(), [[L('Lista', 'List'), L('Accesso per indice', 'Index access')], [L('Dizionario', 'Dictionary'), L('Ricerca per chiave', 'Key lookup')], ['Set', L('Appartenenza veloce', 'Fast membership')], [L('Pila', 'Stack'), 'LIFO'], [L('Coda', 'Queue'), 'FIFO']], L('Le strutture di base.', 'Core structures.')),
            dFlash(pFlash(), [[L('Un set aiuta a trovare doppioni in O(n)', 'A set helps find duplicates in O(n)'), true], [L('Cercare in una lista non ordinata è O(1)', 'Searching an unsorted list is O(1)'), false], [L('La funzione annulla usa spesso una pila', 'Undo often uses a stack'), true], [L('Una coda è LIFO', 'A queue is LIFO'), false]], L('Organizzare per velocità.', 'Organising for speed.')),
        ]),
        lesson('search-sort', L('Ricerca e ordinamento', 'Searching and sorting'), [
            P('La **ricerca lineare** guarda gli elementi uno per uno: O(n). La **ricerca binaria**, su dati **ordinati**, guarda il centro e scarta metà dei dati a ogni passo: O(log n), cioè circa 20 passi per un milione di elementi.', '**Linear search** looks at items one by one: O(n). **Binary search**, on **sorted** data, looks at the middle and discards half the data each step: O(log n), about 20 steps for a million items.'),
            Fig(L(fig_algo_binary('passo', 'obiettivo'), fig_algo_binary('step', 'target')),
                L('Quattro righe con la stessa lista ordinata di 11 numeri, da 3 a 55; si cerca 37. Passo 1: il centro è 25, minore di 37, si scarta la metà sinistra. Passo 2: il centro è 42, maggiore, si scarta la destra. Passo 3: il centro è 30, minore. Passo 4: il centro è 37, trovato.', 'Four rows with the same sorted list of 11 numbers, from 3 to 55; the target is 37. Step 1: the middle is 25, less than 37, the left half is dropped. Step 2: the middle is 42, greater, the right is dropped. Step 3: the middle is 30, less. Step 4: the middle is 37, found.'),
                L('A ogni passo la parte da guardare si dimezza: 11, 5, 2, 1.', 'At every step the part left to look at halves: 11, 5, 2, 1.')),
            C('def ricerca_binaria(lista, obiettivo):
    basso, alto = 0, len(lista) - 1
    while basso <= alto:
        medio = (basso + alto) // 2
        if lista[medio] == obiettivo:
            return medio
        if lista[medio] < obiettivo:
            basso = medio + 1
        else:
            alto = medio - 1
    return -1', 'python'),
            P('Gli algoritmi di ordinamento semplici (bubble sort, insertion sort) sono O(n²); quelli efficienti (merge sort, heapsort, quicksort in media, l’ordinamento di Python, il Timsort che dalla versione 3.11 usa la strategia di fusione Powersort) sono O(n log n). Nella pratica usa l’ordinamento della libreria (`sorted`, `list.sort`): è veloce e collaudato.', 'Simple sorting algorithms (bubble sort, insertion sort) are O(n²); efficient ones (merge sort, heapsort, quicksort on average, Python’s sort, the Timsort that since version 3.11 uses the Powersort merge strategy) are O(n log n). In practice use the library sort (`sorted`, `list.sort`): it is fast and battle-tested.'),
            Fig(fig_algo_bubble(),
                L('Cinque barre alte 5, 1, 4, 2 e 3. Nel bubble sort ogni coppia di vicine fuori ordine si scambia, passata dopo passata, finché le barre sono in ordine da 1 a 5, come nell’immagine ferma.', 'Five bars of height 5, 1, 4, 2 and 3. In bubble sort every pair of neighbours out of order swaps, pass after pass, until the bars are in order from 1 to 5, as in the still image.'),
                L('Il bubble sort confronta solo vicine: tanti scambi, per questo è O(n²).', 'Bubble sort compares only neighbours: many swaps, which is why it is O(n²).')),
        ], [
            Q('b1', L('Che cosa serve per la ricerca binaria?', 'What does binary search need?'), [L('Dati ordinati', 'Sorted data'), L('Dati casuali', 'Random data'), L('Un database', 'A database'), L('Niente', 'Nothing')], 0, L('Altrimenti non si può scartare metà.', 'Otherwise you cannot discard half.')),
            Q('b2', L('Quanti passi circa per un milione di elementi con la ricerca binaria?', 'About how many steps for a million items with binary search?'), ['20', '1000', '1.000.000', '2'], 0, L('2²⁰ ≈ un milione.', '2²⁰ ≈ one million.')),
            Q('b3', L('Che complessità ha un buon algoritmo di ordinamento?', 'What complexity does a good sorting algorithm have?'), ['O(n log n)', 'O(n²)', 'O(1)', 'O(2ⁿ)'], 0, L('Come merge sort.', 'Like merge sort.')),
        ], task('python', L('Completa la ricerca binaria: se `lista[medio] < obiettivo` sposta `basso = medio + 1`, altrimenti `alto = medio - 1`.', 'Complete binary search: if `lista[medio] < obiettivo` move `basso = medio + 1`, otherwise `alto = medio - 1`.'), "while basso <= alto:\n    medio = (basso + alto) // 2\n    if lista[medio] == obiettivo:\n        return medio\n    ", [re('if\s+lista\s*\[\s*medio\s*\]\s*<\s*obiettivo\s*:\s*\n\s+basso\s*=\s*medio\s*\+\s*1', L('Vai a destra', 'Go right')), re('else\s*:\s*\n\s+alto\s*=\s*medio\s*-\s*1', L('Altrimenti a sinistra', 'Otherwise go left'))], '', "if lista[medio] < obiettivo:\n        basso = medio + 1\n    else:\n        alto = medio - 1"), [
            dMatch(pMatch(), [[L('Ricerca lineare', 'Linear search'), 'O(n)'], [L('Ricerca binaria', 'Binary search'), 'O(log n)'], ['Bubble sort', 'O(n²)'], ['Merge sort', 'O(n log n)'], ['sorted()', L('Ordinamento della libreria', 'Library sort')]], L('Cercare e ordinare.', 'Searching and sorting.')),
            dBug(pBug(), ['while basso <= alto:', '    medio = (basso + alto) // 2', '    if lista[medio] < obiettivo:', '        basso = medio', '    else:', '        alto = medio - 1'], 3, '        basso = medio + 1', L('Senza +1 il ciclo può non finire mai.', 'Without +1 the loop may never end.')),
        ]),
        lesson('recursion', L('Ricorsione, alberi e grafi', 'Recursion, trees and graphs'), [
            P('Una funzione **ricorsiva** chiama sé stessa su un problema più piccolo, fino a un **caso base** che si risolve subito. È naturale per strutture annidate come cartelle e **alberi**. Senza caso base la ricorsione non finisce e il programma va in errore.', 'A **recursive** function calls itself on a smaller problem, down to a **base case** solved at once. It is natural for nested structures like folders and **trees**. Without a base case recursion never ends and the program errors out.'),
            C('def fattoriale(n):
    if n <= 1:            # caso base
        return 1
    return n * fattoriale(n - 1)

from collections import deque
def bfs(grafo, inizio):   # visita in ampiezza: trova i percorsi più brevi
    visitati, coda = {inizio}, deque([inizio])
    while coda:
        nodo = coda.popleft()
        for vicino in grafo[nodo]:
            if vicino not in visitati:
                visitati.add(vicino)
                coda.append(vicino)
    return visitati', 'python'),
            P('Un **grafo** è fatto di nodi collegati da archi: reti stradali, amicizie, dipendenze tra pacchetti. La **visita in ampiezza** (BFS) esplora livello per livello con una coda e trova i percorsi con meno passi; la **visita in profondità** (DFS) va il più lontano possibile, spesso con la ricorsione.', 'A **graph** is made of nodes connected by edges: road networks, friendships, package dependencies. **Breadth-first search** (BFS) explores level by level with a queue and finds paths with fewest steps; **depth-first search** (DFS) goes as far as possible, often with recursion.'),
        ], [
            Q('r1', L('Che cos’è il caso base?', 'What is the base case?'), [L('Il caso che si risolve senza ricorsione', 'The case solved without recursion'), L('Il primo argomento', 'The first argument'), L('Un errore', 'An error'), L('Il caso più difficile', 'The hardest case')], 0, L('Ferma la ricorsione.', 'It stops recursion.')),
            Q('r2', L('Quale visita trova il percorso con meno passi?', 'Which search finds the path with fewest steps?'), [L('In ampiezza (BFS)', 'Breadth-first (BFS)'), L('In profondità (DFS)', 'Depth-first (DFS)'), L('A caso', 'Random'), L('Nessuna', 'None')], 0, L('Livello per livello.', 'Level by level.')),
            Q('r3', L('Quale di questi è un grafo?', 'Which of these is a graph?'), [L('Una rete di amicizie', 'A friendship network'), L('Un numero intero', 'An integer'), L('Una singola parola', 'A single word'), L('Un colore', 'A colour')], 0, L('Nodi e archi.', 'Nodes and edges.')),
        ], task('python', L('Scrivi la funzione ricorsiva `somma(n)` che restituisce `0` se `n == 0` e altrimenti `n + somma(n - 1)`.', 'Write the recursive function `somma(n)` returning `0` if `n == 0` and otherwise `n + somma(n - 1)`.'), '', [re('def\s+somma\s*\(\s*n\s*\)', L('La funzione', 'The function')), re('if\s+n\s*(==|<=)\s*0\s*:\s*\n\s+return\s+0', L('Il caso base', 'The base case')), re('return\s+n\s*\+\s*somma\s*\(\s*n\s*-\s*1\s*\)', L('La chiamata ricorsiva', 'The recursive call'))], '', "def somma(n):\n    if n == 0:\n        return 0\n    return n + somma(n - 1)"), [
            dMatch(pMatch(), [[L('Ricorsione', 'Recursion'), L('Funzione che chiama sé stessa', 'Function calling itself')], [L('Caso base', 'Base case'), L('Ferma la ricorsione', 'Stops recursion')], [L('Albero', 'Tree'), L('Struttura gerarchica', 'Hierarchical structure')], [L('Grafo', 'Graph'), L('Nodi e archi', 'Nodes and edges')], ['BFS', L('Percorsi più brevi', 'Shortest paths')]], L('Strutture annidate.', 'Nested structures.')),
            dBug(pBug(), ['def conto_alla_rovescia(n):', '    print(n)', '    conto_alla_rovescia(n - 1)'], 2, '    if n > 0: conto_alla_rovescia(n - 1)', L('Senza caso base la ricorsione non finisce.', 'Without a base case recursion never ends.')),
        ]),
        lesson('dp-greedy', L('Programmazione dinamica e algoritmi greedy', 'Dynamic programming and greedy algorithms'), [
            P('La **programmazione dinamica** risolve problemi che si ripetono in sottoproblemi uguali: salva i risultati già calcolati (**memoizzazione**) invece di rifarli. Fibonacci ricorsivo ingenuo è esponenziale; con una cache diventa lineare.', '**Dynamic programming** solves problems that repeat into identical subproblems: it saves results already computed (**memoisation**) instead of redoing them. Naive recursive Fibonacci is exponential; with a cache it becomes linear.'),
            Fig(L(fig_algo_fib('calcolata', 'presa dal memo'), fig_algo_fib('computed', 'taken from the memo')),
                L('L’albero delle chiamate di f(5): f(5) chiama f(4) e f(3), f(4) chiama f(3) e f(2), e così via fino a f(1) e f(0). Le chiamate ripetute, il secondo f(3) e il secondo f(2), sono tratteggiate: la loro risposta arriva dal memo, senza scendere oltre.', 'The call tree of f(5): f(5) calls f(4) and f(3), f(4) calls f(3) and f(2), and so on down to f(1) and f(0). The repeated calls, the second f(3) and the second f(2), are dashed: their answer comes from the memo, without going further down.'),
                L('Senza memo quei rami si ricalcolerebbero ogni volta, e l’albero raddoppierebbe a ogni livello.', 'Without the memo those branches would be recomputed every time, and the tree would double at every level.')),
            C('const memo = new Map();
function fib(n) {
  if (n < 2) return n;
  if (memo.has(n)) return memo.get(n);
  const r = fib(n - 1) + fib(n - 2);
  memo.set(n, r);
  return r;
}', 'js'),
            P('Un algoritmo **greedy** (goloso) sceglie a ogni passo l’opzione migliore **in quel momento**. È semplice e veloce, ma non sempre ottimo: per dare il resto con monete da 1, 3 e 4, il greedy su 6 dà 4+1+1 (tre monete) invece di 3+3 (due).', 'A **greedy** algorithm picks the best option **at that moment** at each step. It is simple and fast, but not always optimal: to give change with coins of 1, 3 and 4, greedy on 6 gives 4+1+1 (three coins) instead of 3+3 (two).'),
            P('Come riconoscere un problema da programmazione dinamica: si scompone in versioni più piccole di sé stesso che **si ripetono** (lo stesso `fib(3)` calcolato più volte) e la soluzione migliore si costruisce da quelle migliori delle parti. Due strade: dall’alto con il memo, come sopra, o dal basso riempiendo una tabella dai casi più piccoli (`fib(0)`, `fib(1)`, …), senza ricorsione.', 'How to recognise a dynamic programming problem: it splits into smaller versions of itself that **repeat** (the same `fib(3)` computed several times) and the best solution is built from the best solutions of the parts. Two routes: top-down with the memo, as above, or bottom-up filling a table from the smallest cases (`fib(0)`, `fib(1)`, …), without recursion.'),
        ], [
            Q('dp1', L('Che cosa fa la memoizzazione?', 'What does memoisation do?'), [L('Salva i risultati già calcolati', 'Saves results already computed'), L('Ordina i dati', 'Sorts data'), L('Cancella la ricorsione', 'Deletes recursion'), L('Usa meno variabili', 'Uses fewer variables')], 0, L('Niente calcoli ripetuti.', 'No repeated work.')),
            Q('dp2', L('Fibonacci con memoizzazione diventa…', 'Fibonacci with memoisation becomes…'), ['O(n)', 'O(2ⁿ)', 'O(n²)', 'O(1)'], 0, L('Ogni valore si calcola una volta.', 'Each value is computed once.')),
            Q('dp3', L('Un algoritmo greedy è sempre ottimo?', 'Is a greedy algorithm always optimal?'), [L('No, dipende dal problema', 'No, it depends on the problem'), L('Sì, sempre', 'Yes, always'), L('Solo con i numeri pari', 'Only with even numbers'), L('Solo se ricorsivo', 'Only if recursive')], 0, L('Vedi l’esempio delle monete.', 'See the coin example.')),
        ], task('js', L('In `fib`, prima di calcolare, restituisci `memo.get(n)` se `memo.has(n)`.', 'In `fib`, before computing, return `memo.get(n)` if `memo.has(n)`.'), '', [re('if\s*\(\s*memo\.has\(\s*n\s*\)\s*\)\s*\{?\s*return\s+memo\.get\(\s*n\s*\)', L('Usi la cache', 'You use the cache'))], '', 'if (memo.has(n)) return memo.get(n);'), [
            dOrder(pSort(), ['function fib(n) {', '  if (n < 2) return n;', '  if (memo.has(n)) return memo.get(n);', '  const r = fib(n - 1) + fib(n - 2);', '  memo.set(n, r);', '  return r;', '}'], L('Caso base, cache, calcolo, salvataggio.', 'Base case, cache, compute, save.')),
            dFlash(pFlash(), [[L('La programmazione dinamica riusa i sottoproblemi', 'Dynamic programming reuses subproblems'), true], [L('Il greedy trova sempre l’ottimo', 'Greedy always finds the optimum'), false], [L('Fibonacci ingenuo è esponenziale', 'Naive Fibonacci is exponential'), true], [L('La memoizzazione rende il codice più lento', 'Memoisation makes code slower'), false]], L('Strategie di soluzione.', 'Solving strategies.')),
        ]),
        lesson('bst', L('Alberi binari di ricerca', 'Binary search trees'), [
            P('In un **albero binario di ricerca** (BST) ogni nodo ha al massimo due figli: i valori **più piccoli a sinistra**, quelli più grandi a destra. Cercare e inserire costa O(log n) se l’albero è **bilanciato**, ma O(n) se degenera in una lista (per esempio inserendo dati già ordinati). Gli alberi bilanciati (AVL, rosso-neri) restano in O(log n), e i database usano i B-tree per gli indici.', 'In a **binary search tree** (BST) each node has at most two children: **smaller values on the left**, larger ones on the right. Searching and inserting cost O(log n) if the tree is **balanced**, but O(n) if it degenerates into a list (for example when inserting already sorted data). Balanced trees (AVL, red-black) stay at O(log n), and databases use B-trees for indexes.'),
            Fig(L(fig_algo_bst(['bilanciato: O(log n)', 'cercare 40: 50 → 30 → 40', 'degenerato: O(n)', 'inserendo 20, 30, 40, 50, 70', 'più piccoli a sinistra']), fig_algo_bst(['balanced: O(log n)', 'search 40: 50 → 30 → 40', 'degenerate: O(n)', 'inserting 20, 30, 40, 50, 70', 'smaller on the left'])),
                L('A sinistra un albero bilanciato con radice 50, figli 30 e 70, e sotto 30 i figli 20 e 40; il percorso per cercare 40, da 50 a 30 a 40, è evidenziato. A destra gli stessi valori inseriti già in ordine formano una scala: ogni nodo ha solo il figlio destro.', 'On the left a balanced tree with root 50, children 30 and 70, and under 30 the children 20 and 40; the path to search 40, from 50 to 30 to 40, is highlighted. On the right the same values inserted already sorted form a staircase: each node has only a right child.'),
                L('Stessi valori, forma diversa: la forma decide quanti passi servono.', 'Same values, different shape: the shape decides how many steps you need.')),
            C('class Nodo:
    def __init__(self, valore):
        self.valore, self.sinistra, self.destra = valore, None, None

def inserisci(radice, valore):
    if radice is None:
        return Nodo(valore)
    if valore < radice.valore:
        radice.sinistra = inserisci(radice.sinistra, valore)
    else:
        radice.destra = inserisci(radice.destra, valore)
    return radice

def in_ordine(nodo):                  # sinistra, nodo, destra
    if nodo:
        yield from in_ordine(nodo.sinistra)
        yield nodo.valore
        yield from in_ordine(nodo.destra)

radice = None
for v in [50, 30, 70, 20, 40]:
    radice = inserisci(radice, v)
print(list(in_ordine(radice)))        # [20, 30, 40, 50, 70]', 'python'),
            P('Le visite: **in ordine** (in un BST dà i valori ordinati), **anticipata** (prima il nodo: per copiare un albero), **posticipata** (prima i figli: per calcolare la dimensione delle cartelle) e **per livelli** (la BFS, con una coda).', 'The traversals: **in-order** (in a BST it yields the values sorted), **pre-order** (node first: to copy a tree), **post-order** (children first: to compute folder sizes) and **level-order** (BFS, with a queue).'),
        ], [
            Q('bt1', L('In un BST, dove stanno i valori più piccoli di un nodo?', 'In a BST, where are the values smaller than a node?'), [L('Nel sottoalbero di destra', 'In the right subtree'), L('Nel sottoalbero di sinistra', 'In the left subtree'), L('Sempre nella radice', 'Always in the root'), L('Sparsi nelle foglie a caso', 'Scattered randomly in the leaves')], 1, L('Sinistra minori, destra maggiori.', 'Smaller left, larger right.')),
            Q('bt2', L('Che cosa dà la visita in ordine di un BST?', 'What does an in-order traversal of a BST yield?'), [L('I valori in ordine casuale', 'The values in random order'), L('Solo le foglie', 'Only the leaves'), L('I valori in ordine crescente', 'The values in ascending order'), L('Solo la radice e i suoi figli', 'Only the root and its children')], 2, L('Sinistra, nodo, destra.', 'Left, node, right.')),
            Q('bt3', L('Inserisci 1, 2, 3, 4, 5 in quest’ordine in un BST semplice. Quanto costa poi una ricerca?', 'You insert 1, 2, 3, 4, 5 in this order into a plain BST. What does a search then cost?'), ['O(1)', 'O(log n)', 'O(n log n)', 'O(n)'], 3, L('L’albero è diventato una lista che scende a destra.', 'The tree has become a list going down to the right.')),
        ], task('python', L('Scrivi la funzione ricorsiva `cerca(nodo, valore)`: `False` se il nodo è `None`, `True` se il valore è quello del nodo, altrimenti cerca a sinistra o a destra.', 'Write the recursive function `cerca(nodo, valore)`: `False` if the node is `None`, `True` if the value is the node’s, otherwise search left or right.'), '', [re('def\s+cerca\s*\(\s*nodo\s*,\s*valore\s*\)', L('La funzione', 'The function')), re('if\s+(nodo\s+is\s+None|not\s+nodo)\s*:\s*\n\s+return\s+False', L('Il caso base', 'The base case')), re('cerca\s*\(\s*nodo\.sinistra\s*,\s*valore\s*\)', L('Cerchi a sinistra', 'You search left')), re('cerca\s*\(\s*nodo\.destra\s*,\s*valore\s*\)', L('Cerchi a destra', 'You search right'))], '', "def cerca(nodo, valore):\n    if nodo is None:\n        return False\n    if valore == nodo.valore:\n        return True\n    if valore < nodo.valore:\n        return cerca(nodo.sinistra, valore)\n    return cerca(nodo.destra, valore)"), [
            dMatch(pMatch(), [[L('Radice', 'Root'), L('Il nodo in cima', 'The top node')], [L('Foglia', 'Leaf'), L('Un nodo senza figli', 'A node with no children')], ['BST', L('Minori a sinistra, maggiori a destra', 'Smaller left, larger right')], [L('In ordine', 'In-order'), L('Valori ordinati', 'Sorted values')], [L('Albero bilanciato', 'Balanced tree'), L('Resta in O(log n)', 'Stays at O(log n)')]], L('Il vocabolario degli alberi.', 'Tree vocabulary.')),
            dFlash(pFlash(), [[L('Cercare in un BST costa sempre O(log n)', 'Searching a BST always costs O(log n)'), false], [L('La visita in ordine di un BST dà i valori ordinati', 'An in-order traversal of a BST yields sorted values'), true], [L('Gli indici dei database usano spesso i B-tree', 'Database indexes often use B-trees'), true], [L('La visita anticipata visita i figli prima del nodo', 'Pre-order visits the children before the node'), false]], L('Rami ordinati.', 'Tidy branches.')),
        ]),
        lesson('heaps', L('Heap e code con priorità', 'Heaps and priority queues'), [
            P('Un **heap** (coda con priorità) tiene sempre in cima l’elemento più piccolo (min-heap): leggerlo costa O(1), estrarlo o inserirne uno nuovo O(log n). In Python c’è il modulo `heapq`, che lavora su una normale lista. Serve per i compiti in ordine di urgenza, per i “primi k”, per unire liste ordinate e per l’algoritmo di Dijkstra.', 'A **heap** (priority queue) always keeps the smallest element on top (min-heap): reading it costs O(1), extracting it or inserting a new one O(log n). Python has the `heapq` module, which works on an ordinary list. It is used for tasks in order of urgency, for the “top k”, for merging sorted lists and for Dijkstra’s algorithm.'),
            Fig(L(fig_algo_heap('in cima (posizione 0) c’è sempre il più piccolo'), fig_algo_heap('the smallest is always on top (position 0)')),
                L('Un min-heap con i valori 1, 3, 6, 7, 8, 9 disegnato come albero, con 1 in cima, e accanto la lista che lo contiene, posizioni da 0 a 5. Due archi mostrano che i figli della posizione i stanno in 2i+1 e 2i+2.', 'A min-heap with the values 1, 3, 6, 7, 8, 9 drawn as a tree, with 1 on top, and next to it the list that holds it, positions 0 to 5. Two arcs show that the children of position i are at 2i+1 and 2i+2.'),
                L('Ogni genitore è minore dei suoi figli; tra fratelli non c’è un ordine.', 'Every parent is smaller than its children; between siblings there is no order.')),
            C('import heapq

compiti = []
heapq.heappush(compiti, (2, "rispondere alle email"))
heapq.heappush(compiti, (1, "correggere il bug in produzione"))
heapq.heappush(compiti, (3, "aggiornare la documentazione"))
print(heapq.heappop(compiti))     # (1, \'correggere il bug in produzione\')

voti = [7, 3, 9, 1, 8, 6]
print(heapq.nlargest(3, voti))    # [9, 8, 7]
print(heapq.nsmallest(2, voti))   # [1, 3]', 'python'),
            P('Ordinare un milione di elementi per prenderne i primi 3 costa O(n log n); con un heap di dimensione k costa O(n log k). `heapq` è un min-heap: per avere prima il massimo si inseriscono i numeri **cambiati di segno**. Le tuple si confrontano campo per campo, quindi la priorità va per prima.', 'Sorting a million items to take the top 3 costs O(n log n); with a heap of size k it costs O(n log k). `heapq` is a min-heap: to get the maximum first you insert the numbers **with their sign flipped**. Tuples are compared field by field, so the priority goes first.'),
        ], [
            Q('hp1', L('Che cosa restituisce `heappop` da un min-heap?', 'What does `heappop` return from a min-heap?'), [L('L’elemento più grande', 'The largest element'), L('L’ultimo elemento inserito', 'The last element inserted'), L('L’elemento più piccolo', 'The smallest element'), L('Un elemento a caso', 'A random element')], 2, L('Il minimo sta sempre in cima.', 'The minimum is always on top.')),
            Q('hp2', L('Quanto costa inserire un elemento in un heap?', 'What does inserting an element into a heap cost?'), ['O(1)', 'O(log n)', 'O(n)', 'O(n log n)'], 1, L('Risale l’albero fino al suo posto.', 'It climbs the tree to its place.')),
            Q('hp3', L('`heapq` è un min-heap. Come ottieni prima il massimo?', '`heapq` is a min-heap. How do you get the maximum first?'), [L('Con la funzione `heapq.reverse`', 'With the `heapq.reverse` function'), L('Ordinando la lista dopo ogni inserimento', 'Sorting the list after every insertion'), L('Non si può fare in Python', 'It cannot be done in Python'), L('Inserendo i numeri cambiati di segno', 'Inserting the numbers with their sign flipped')], 3, L('Il più grande diventa il più piccolo.', 'The largest becomes the smallest.')),
        ], task('python', L('Importa `heapq` e salva in `migliori` i 5 valori più alti della lista `punteggi`, usando `nlargest`.', 'Import `heapq` and store in `migliori` the 5 highest values of the `punteggi` list, using `nlargest`.'), "punteggi = [88, 42, 97, 65, 73, 91, 55, 80]\n", [re('import\s+heapq|from\s+heapq\s+import', L('Importi heapq', 'You import heapq')), re('migliori\s*=\s*(heapq\.)?nlargest\s*\(\s*5\s*,\s*punteggi\s*\)', L('I 5 più alti', 'The top 5'))], '', "import heapq\nmigliori = heapq.nlargest(5, punteggi)"), [
            dMatch(pMatch(), [['heappush', L('Inserisce in O(log n)', 'Inserts in O(log n)')], ['heappop', L('Estrae il minimo', 'Extracts the minimum')], ['nlargest', L('I k più grandi', 'The k largest')], ['Min-heap', L('Il minimo in cima', 'The minimum on top')], [L('Coda con priorità', 'Priority queue'), L('Prima il più urgente', 'Most urgent first')]], L('Prima le cose importanti.', 'Important things first.')),
            dBlanks(pFill(), "import heapq\ncoda = []\nheapq.___(coda, (2, \"email\"))\nprimo = heapq.___(coda)", ['heappush', 'heappop'], ['append', 'pop', 'sort'], L('Si inserisce con heappush e si estrae con heappop.', 'You insert with heappush and extract with heappop.')),
        ]),
        lesson('two-pointers', L('Due puntatori e finestra scorrevole', 'Two pointers and sliding window'), [
            P('Due tecniche frequenti trasformano O(n²) in O(n). Con i **due puntatori**, su una lista ordinata, un indice parte dall’inizio e uno dalla fine e si avvicinano: così trovi una coppia con una certa somma. Con la **finestra scorrevole** guardi k elementi consecutivi e, a ogni passo, aggiungi quello che entra e togli quello che esce, invece di ricalcolare tutto.', 'Two common techniques turn O(n²) into O(n). With **two pointers**, on a sorted list, one index starts at the beginning and one at the end and they move towards each other: that way you find a pair with a given sum. With the **sliding window** you look at k consecutive items and, at each step, add the one coming in and remove the one going out, instead of recomputing everything.'),
            Fig(L(fig_algo_window('somma', 'la migliore'), fig_algo_window('sum', 'the best')),
                L('La lista 2, 1, 5, 1, 3, 2 con una cornice larga tre caselle che scorre di una posizione alla volta. Le somme sono 8, 7, 9 e 6; la cornice ferma sta su 5, 1, 3, la somma migliore, 9.', 'The list 2, 1, 5, 1, 3, 2 with a frame three cells wide that slides one position at a time. The sums are 8, 7, 9 and 6; the still frame sits on 5, 1, 3, the best sum, 9.'),
                L('Spostando la finestra entra un valore ed esce un altro: niente da ricalcolare.', 'Moving the window, one value enters and another leaves: nothing to recompute.')),
            C('def coppia_con_somma(ordinata, obiettivo):   # due puntatori: O(n)
    i, j = 0, len(ordinata) - 1
    while i < j:
        s = ordinata[i] + ordinata[j]
        if s == obiettivo:
            return ordinata[i], ordinata[j]
        if s < obiettivo:
            i += 1
        else:
            j -= 1
    return None

def somma_massima(valori, k):                 # finestra scorrevole: O(n)
    finestra = sum(valori[:k])
    migliore = finestra
    for i in range(k, len(valori)):
        finestra += valori[i] - valori[i - k]
        migliore = max(migliore, finestra)
    return migliore

print(coppia_con_somma([1, 3, 4, 6, 9], 10))  # (1, 9)
print(somma_massima([2, 1, 5, 1, 3, 2], 3))   # 9', 'python'),
            P('Quando un problema parla di “sottoliste consecutive”, “coppia in una lista ordinata” o “sottostringa più lunga senza ripetizioni”, pensa a queste tecniche: una sola passata sui dati invece di due cicli annidati.', 'When a problem talks about “consecutive sublists”, “a pair in a sorted list” or “the longest substring without repeats”, think of these techniques: a single pass over the data instead of two nested loops.'),
        ], [
            Q('tp1', L('Perché la ricerca della coppia con due puntatori vuole una lista ordinata?', 'Why does the two-pointer pair search need a sorted list?'), [L('Perché Python non sa scorrere liste disordinate', 'Because Python cannot loop over unsorted lists'), L('Per poter usare la ricerca binaria dentro il ciclo', 'To be able to use binary search inside the loop'), L('Per sapere quale puntatore spostare in base alla somma', 'To know which pointer to move based on the sum'), L('Per avere meno elementi da controllare', 'To have fewer elements to check')], 2, L('Somma troppo piccola: avanza a sinistra; troppo grande: arretra a destra.', 'Sum too small: move left forward; too large: move right back.')),
            Q('tp2', L('Quanto costa la finestra scorrevole su n elementi?', 'What does the sliding window cost over n items?'), ['O(n·k)', 'O(n²)', 'O(log n)', 'O(n)'], 3, L('Ogni elemento entra ed esce una volta.', 'Each item enters and leaves once.')),
            Q('tp3', L('Nella finestra scorrevole, che cosa fai a ogni passo?', 'In the sliding window, what do you do at each step?'), [L('Ricalcoli la somma di tutta la finestra', 'Recompute the sum of the whole window'), L('Aggiungi l’elemento che entra e togli quello che esce', 'Add the item coming in and remove the one going out'), L('Riordini tutta la lista', 'Re-sort the whole list'), L('Raddoppi la larghezza della finestra', 'Double the window’s width')], 1, L('Da O(k) a O(1) per passo.', 'From O(k) to O(1) per step.')),
        ], task('python', L('Completa il ciclo: aggiorna `finestra` aggiungendo `valori[i]` e togliendo `valori[i - k]`, poi `migliore = max(migliore, finestra)`.', 'Complete the loop: update `finestra` adding `valori[i]` and removing `valori[i - k]`, then `migliore = max(migliore, finestra)`.'), "def somma_massima(valori, k):\n    finestra = sum(valori[:k])\n    migliore = finestra\n    for i in range(k, len(valori)):\n        # aggiorna la finestra e il migliore\n    return migliore", [re('finestra\s*(\+=\s*valori\s*\[\s*i\s*\]\s*-\s*valori\s*\[\s*i\s*-\s*k\s*\]|=\s*finestra\s*\+\s*valori\s*\[\s*i\s*\]\s*-\s*valori\s*\[\s*i\s*-\s*k\s*\])', L('Fai scorrere la finestra', 'You slide the window')), re('migliore\s*=\s*max\s*\(\s*(migliore\s*,\s*finestra|finestra\s*,\s*migliore)\s*\)', L('Tieni il migliore', 'You keep the best'))], '', "        finestra += valori[i] - valori[i - k]\n        migliore = max(migliore, finestra)"), [
            dMatch(pMatch(), [[L('Due puntatori', 'Two pointers'), L('Dagli estremi verso il centro', 'From the ends towards the middle')], [L('Finestra scorrevole', 'Sliding window'), L('Elementi consecutivi', 'Consecutive items')], [L('Lista ordinata', 'Sorted list'), L('Serve ai due puntatori', 'Needed by two pointers')], ['O(n)', L('Una sola passata', 'A single pass')], ['valori[i - k]', L('L’elemento che esce', 'The item going out')]], L('Una passata basta.', 'One pass is enough.')),
            dBug(pBug(), ['while i < j:', '    s = ordinata[i] + ordinata[j]', '    if s < obiettivo:', '        i -= 1', '    else:', '        j -= 1'], 3, '        i += 1', L('Se la somma è piccola il puntatore sinistro deve avanzare.', 'If the sum is small the left pointer must move forward.')),
        ]),
        lesson('dijkstra', L('Cammini minimi: Dijkstra', 'Shortest paths: Dijkstra'), [
            P('In un grafo **pesato** (distanze, tempi, costi) la BFS non basta: il percorso con meno tappe non è sempre il più corto. L’algoritmo di **Dijkstra** trova i cammini minimi da un nodo quando i pesi non sono negativi: estrae ogni volta da un heap il nodo più vicino non ancora sistemato e aggiorna i suoi vicini. Con un heap costa O((V + E) log V).', 'In a **weighted** graph (distances, times, costs) BFS is not enough: the path with fewest stops is not always the shortest. **Dijkstra’s** algorithm finds the shortest paths from a node when weights are not negative: each time it extracts from a heap the nearest node not yet settled and updates its neighbours. With a heap it costs O((V + E) log V).'),
            Fig(fig_algo_dijkstra(),
                L('Tre città: Udine, Pordenone e Venezia. Da Udine a Pordenone 50 minuti, da Pordenone a Venezia 70, da Udine a Venezia direttamente 130. Il percorso evidenziato passa da Pordenone: la distanza di Venezia è 120, non 130.', 'Three cities: Udine, Pordenone and Venezia. From Udine to Pordenone 50 minutes, from Pordenone to Venezia 70, from Udine to Venezia directly 130. The highlighted path goes through Pordenone: Venezia’s distance is 120, not 130.'),
                L('Meno tappe non vuol dire più breve: Dijkstra somma i pesi.', 'Fewer stops does not mean shorter: Dijkstra adds up the weights.')),
            C('import heapq

def dijkstra(grafo, inizio):
    distanze = {inizio: 0}
    coda = [(0, inizio)]
    while coda:
        d, nodo = heapq.heappop(coda)
        if d > distanze[nodo]:
            continue                      # voce vecchia, già migliorata
        for vicino, peso in grafo[nodo].items():
            nuova = d + peso
            if nuova < distanze.get(vicino, float("inf")):
                distanze[vicino] = nuova
                heapq.heappush(coda, (nuova, vicino))
    return distanze

minuti = {
    "Udine":     {"Pordenone": 50, "Venezia": 130},
    "Pordenone": {"Venezia": 70},
    "Venezia":   {},
}
print(dijkstra(minuti, "Udine"))   # {\'Udine\': 0, \'Pordenone\': 50, \'Venezia\': 120}', 'python'),
            P('Qui la strada diretta per Venezia (130) perde contro quella che passa da Pordenone (50 + 70). Con pesi negativi Dijkstra sbaglia: serve Bellman-Ford. Mappe e navigatori usano varianti come **A***, che aggiungono una stima della distanza mancante per esplorare meno nodi.', 'Here the direct road to Venice (130) loses to the one through Pordenone (50 + 70). With negative weights Dijkstra goes wrong: you need Bellman-Ford. Maps and satnavs use variants such as **A***, which add an estimate of the remaining distance to explore fewer nodes.'),
        ], [
            Q('dj1', L('Perché la BFS non basta su una mappa con i chilometri?', 'Why is BFS not enough on a map with kilometres?'), [L('Perché il percorso con meno tappe non è sempre il più corto', 'Because the path with fewest stops is not always the shortest'), L('Perché la BFS funziona solo sugli alberi', 'Because BFS only works on trees'), L('Perché la BFS non usa una coda', 'Because BFS does not use a queue'), L('Perché le mappe hanno troppi nodi', 'Because maps have too many nodes')], 0, L('La BFS conta i passi, non i pesi.', 'BFS counts steps, not weights.')),
            Q('dj2', L('Quale struttura usa Dijkstra per scegliere il nodo più vicino?', 'Which structure does Dijkstra use to pick the nearest node?'), [L('Una pila', 'A stack'), L('Un set', 'A set'), L('Una coda con priorità (heap)', 'A priority queue (heap)'), L('Una lista riordinata a mano a ogni passo', 'A list re-sorted by hand at every step')], 2, L('Estrarre il minimo costa O(log n).', 'Extracting the minimum costs O(log n).')),
            Q('dj3', L('Quando Dijkstra dà risultati sbagliati?', 'When does Dijkstra give wrong results?'), [L('Con grafi molto grandi', 'With very large graphs'), L('Con nodi chiamati con delle lettere', 'With nodes named with letters'), L('Con grafi senza cicli', 'With graphs without cycles'), L('Con archi di peso negativo', 'With negative-weight edges')], 3, L('Allora serve Bellman-Ford.', 'Then you need Bellman-Ford.')),
        ], task('python', L('Completa l’aggiornamento: se `nuova < distanze.get(vicino, float("inf"))`, salva `distanze[vicino] = nuova` e inserisci `(nuova, vicino)` nell’heap `coda`.', 'Complete the update: if `nuova < distanze.get(vicino, float("inf"))`, store `distanze[vicino] = nuova` and push `(nuova, vicino)` onto the `coda` heap.'), "for vicino, peso in grafo[nodo].items():\n    nuova = d + peso\n    # aggiorna se è migliore\n", [re('if\s+nuova\s*<\s*distanze\.get\s*\(\s*vicino\s*,\s*(float\s*\(\s*["\']inf["\']\s*\)|math\.inf)\s*\)\s*:', L('Confronti le distanze', 'You compare the distances')), re('distanze\s*\[\s*vicino\s*\]\s*=\s*nuova', L('Salvi la nuova distanza', 'You store the new distance')), re('heapq\.heappush\s*\(\s*coda\s*,\s*\(\s*nuova\s*,\s*vicino\s*\)\s*\)', L('Lo rimetti nell’heap', 'You push it onto the heap'))], '', "for vicino, peso in grafo[nodo].items():\n    nuova = d + peso\n    if nuova < distanze.get(vicino, float(\"inf\")):\n        distanze[vicino] = nuova\n        heapq.heappush(coda, (nuova, vicino))"), [
            dMatch(pMatch(), [[L('Peso', 'Weight'), L('Il costo di un arco', 'The cost of an edge')], ['Dijkstra', L('Cammini minimi, pesi non negativi', 'Shortest paths, non-negative weights')], ['Heap', L('Il nodo più vicino', 'The nearest node')], ['Bellman-Ford', L('Ammette pesi negativi', 'Allows negative weights')], ['A*', L('Usa una stima della distanza mancante', 'Uses an estimate of the remaining distance')]], L('Arrivare prima.', 'Getting there first.')),
            dOrder(pSort(), [L('Distanza 0 alla partenza, infinito agli altri', 'Distance 0 at the start, infinity elsewhere'), L('Estrai dall’heap il nodo più vicino', 'Extract the nearest node from the heap'), L('Aggiorna le distanze dei suoi vicini', 'Update its neighbours’ distances'), L('Rimetti nell’heap i vicini migliorati', 'Push the improved neighbours onto the heap'), L('Ripeti finché l’heap è vuoto', 'Repeat until the heap is empty')], L('Il ciclo di Dijkstra.', 'Dijkstra’s loop.')),
        ]),
    ],
];
