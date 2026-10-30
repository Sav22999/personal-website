<?php
function pickWeighted(array $weights) {
    $total = array_sum($weights);
    $r = random_int(1, $total);
    foreach ($weights as $value => $weight) {
        $r -= $weight;
        if ($r <= 0) return $value;
    }
    return array_key_first($weights);
}

function pickOne(array $values) {
    return $values[random_int(0, count($values) - 1)];
}

const MALE_NAMES = [
    'James', 'Oliver', 'Liam', 'Noah', 'Ethan', 'Mason', 'Lucas', 'Aiden',
    'Jack', 'Henry', 'Leo', 'Oscar', 'Max', 'Felix', 'Hugo', 'Kai',
    'Ravi', 'Marco', 'Kenji', 'Sven', 'Diego', 'Andre', 'Pavel', 'Finn',
    'Theo', 'Milo', 'Omar', 'Tariq',
];
const FEMALE_NAMES = [
    'Emma', 'Olivia', 'Sophia', 'Mia', 'Luna', 'Ella', 'Aria', 'Chloe',
    'Lily', 'Zoe', 'Nora', 'Ivy', 'Maya', 'Ruby', 'Leila', 'Yuna',
    'Freya', 'Clara', 'Rosa', 'Priya', 'Nina', 'Jade', 'Iris', 'Wren',
    'Sage', 'Amara', 'Lena', 'Ada',
];

const HATS_COVERING_HEAD = ['beanie', 'cap', 'fedora'];
const HAIR_INCOMPATIBLE_WITH_HAT = ['mohawk', 'spiky', 'bun', 'curly'];
const HAIR_INCOMPATIBLE_WITH_HEADBAND = ['bald', 'buzz', 'mohawk'];

function generateCharacter($gender, $name) {
    $char = [
        'name' => $name,
        'gender' => $gender,
        'faceShape' => pickOne(['round', 'oval', 'square', 'heart', 'long']),
        'skinTone' => pickOne(['light', 'fair', 'medium', 'tan', 'brown', 'dark']),
        'eyeColor' => pickWeighted(['brown' => 40, 'blue' => 20, 'green' => 15, 'hazel' => 15, 'gray' => 10]),
        'eyebrows' => pickOne(['thin', 'medium', 'thick']),
        'nose' => pickOne(['small', 'medium', 'large']),
        'mouth' => pickOne(['smile', 'neutral', 'grin']),
        'hairColor' => pickWeighted(['black' => 28, 'brown' => 28, 'blonde' => 18, 'red' => 10, 'gray' => 10, 'white' => 6]),
        'shirtColor' => pickOne(['red', 'blue', 'green', 'yellow', 'purple', 'orange', 'black', 'white']),
        'glasses' => pickWeighted(['none' => 62, 'round' => 13, 'square' => 13, 'sunglasses' => 12]),
        'hat' => pickWeighted(['none' => 68, 'beanie' => 8, 'cap' => 9, 'fedora' => 7, 'headband' => 8]),
        'freckles' => random_int(1, 100) <= 15,
    ];

    if ($gender === 'male') {
        $char['hairStyle'] = pickWeighted(['short' => 22, 'buzz' => 12, 'curly' => 12, 'bald' => 10, 'spiky' => 10, 'slicked' => 14, 'side-part' => 14, 'mohawk' => 6]);
        $char['facialHair'] = pickWeighted(['none' => 42, 'stubble' => 14, 'mustache' => 14, 'beard' => 20, 'goatee' => 10]);
        $char['makeup'] = 'none';
        $char['earrings'] = random_int(1, 100) <= 12 ? 'studs' : 'none';
    } else {
        $char['hairStyle'] = pickWeighted(['long-straight' => 18, 'long-curly' => 13, 'wavy' => 13, 'bob' => 14, 'ponytail' => 12, 'bun' => 10, 'pixie' => 10, 'braids' => 10]);
        $char['facialHair'] = 'none';
        $char['makeup'] = pickWeighted(['none' => 40, 'lipstick' => 22, 'eyeshadow' => 18, 'full' => 20]);
        $char['earrings'] = pickWeighted(['none' => 45, 'studs' => 20, 'hoops' => 20, 'drops' => 15]);
        if ($char['hat'] === 'fedora' && random_int(0, 1)) $char['hat'] = 'none';
    }

    if (in_array($char['hat'], HATS_COVERING_HEAD, true) && in_array($char['hairStyle'], HAIR_INCOMPATIBLE_WITH_HAT, true)) {
        $char['hat'] = 'none';
    }
    if ($char['hat'] === 'headband' && in_array($char['hairStyle'], HAIR_INCOMPATIBLE_WITH_HEADBAND, true)) {
        $char['hat'] = 'none';
    }

    // White hair reads as elderly: keep it off very playful styles.
    if ($char['hairColor'] === 'white' && in_array($char['hairStyle'], ['mohawk', 'spiky', 'pixie', 'braids'], true)) {
        $char['hairColor'] = 'gray';
    }

    return $char;
}

function characterSignature($c) {
    return implode('|', [
        $c['gender'], $c['skinTone'], $c['hairStyle'], $c['hairColor'],
        $c['glasses'], $c['hat'], $c['facialHair'], $c['makeup'],
    ]);
}

function generateCharacters($count = 24) {
    $half = intdiv($count, 2);
    $genders = array_merge(array_fill(0, $half, 'male'), array_fill(0, $count - $half, 'female'));
    shuffle($genders);

    $maleNames = MALE_NAMES;
    $femaleNames = FEMALE_NAMES;
    shuffle($maleNames);
    shuffle($femaleNames);

    $characters = [];
    $signatures = [];

    foreach ($genders as $gender) {
        $name = $gender === 'male' ? array_pop($maleNames) : array_pop($femaleNames);
        for ($attempt = 0; $attempt < 50; $attempt++) {
            $char = generateCharacter($gender, $name);
            $sig = characterSignature($char);
            if (!isset($signatures[$sig])) break;
        }
        $signatures[$sig] = true;
        $characters[] = $char;
    }

    return $characters;
}
