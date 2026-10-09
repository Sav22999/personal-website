<?php
$current_page = 'about-me';
$title = 'About me';
$description = 'Timeline of Saverio Morelli\'s journey — education, career, volunteering, and open-source contributions from 1999 to today.';
$canonical = '/about-me/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once(dirname(__DIR__) . '/include/head.php'); ?>
</head>
<body>

<?php include_once(dirname(__DIR__) . '/include/nav.php'); ?>

<section class="about-hero">
    <div class="about-hero-content">
        <div class="about-hero-text">
            <p class="section-label">About me</p>
            <h1>Hi, I'm Saverio.</h1>
            <p class="about-hero-sub">Frontend developer and UX designer based in Italy. I build open-source apps, browser extensions, and digital tools — always free, always private, always ad-free.</p>
            <div class="about-highlights">
                <div class="about-highlight">
                    <span class="about-highlight-value">10+</span>
                    <span class="about-highlight-label">Projects</span>
                </div>
                <div class="about-highlight">
                    <span class="about-highlight-value">100K+</span>
                    <span class="about-highlight-label">Users</span>
                </div>
                <div class="about-highlight">
                    <span class="about-highlight-value">2017</span>
                    <span class="about-highlight-label">Open source since</span>
                </div>
            </div>
        </div>
        <div class="about-hero-photo">
            <img src="/images/profile.jpeg" alt="Saverio Morelli">
        </div>
    </div>
</section>

<section class="section">
    <h2 class="about-section-title">My journey</h2>

    <div class="timeline">
        <div class="timeline-item">
            <span class="timeline-badge badge-education">Education</span>
            <p class="timeline-date">April 2026</p>
            <p class="timeline-text">I graduated in Multimedia Communication and Information Technologies (M.Sc.) at <a href="https://www.uniud.it/en">University of Udine</a> with grade 110/110, cum laude</p>
        </div>
        <div class="timeline-item">
            <span class="timeline-badge badge-other">Other</span>
            <p class="timeline-date">December 2025</p>
            <p class="timeline-text">I became Indoor Volleyball Referee (FIPAV)</p>
        </div>
        <div class="timeline-item">
            <span class="timeline-badge badge-education">Education</span>
            <p class="timeline-date">September 2024</p>
            <p class="timeline-text">Started the Master's degree in <a href="https://www.uniud.it/en">Computer Science</a> at University of Udine</p>
        </div>
        <div class="timeline-item">
            <span class="timeline-badge badge-work">Work</span>
            <p class="timeline-date">April 2024</p>
            <p class="timeline-text">I started to work at <a href="https://www.fbk.eu">Fondazione Bruno Kessler (FBK)</a> as Frontend App Developer and UX Designer</p>
        </div>
        <div class="timeline-item">
            <span class="timeline-badge badge-education">Education</span>
            <p class="timeline-date">March 2024</p>
            <p class="timeline-text">I graduated in Computer Science (B.Sc.) at <a href="https://www.unitn.it/en">University of Trento</a></p>
        </div>
        <div class="timeline-item">
            <span class="timeline-badge badge-volunteering">Volunteering</span>
            <p class="timeline-date">November 2023</p>
            <p class="timeline-text">I became a volunteer of <a href="https://www.automutuoaiuto.it/">A.M.A. &ndash; Auto Mutuo Aiuto</a>, in the project <a href="https://tra-di-noi.com">TRA-di-NOI</a></p>
        </div>
        <div class="timeline-item">
            <span class="timeline-badge badge-work">Work</span>
            <p class="timeline-date">September 2022 &mdash; August 2023</p>
            <p class="timeline-text">I worked as Computer Science teacher at a <a href="https://www.alberghierorovereto.it">Professional high school</a> in Rovereto</p>
        </div>
        <div class="timeline-item">
            <span class="timeline-badge badge-work">Work</span>
            <p class="timeline-date">January 2022 &mdash; July 2022</p>
            <p class="timeline-text">I worked as Maths teacher at a <a href="https://www.alberghierorovereto.it">Professional high school</a> in Rovereto</p>
        </div>
        <div class="timeline-item">
            <span class="timeline-badge badge-work">Work</span>
            <p class="timeline-date">October 2021</p>
            <p class="timeline-text">Started working as swimming instructor at <a href="https://www.buonconsiglionuoto.it">Buonconsiglio Nuoto</a> in Trento</p>
        </div>
        <div class="timeline-item minor">
            <p class="timeline-date">September 2021 &mdash; January 2022</p>
            <p class="timeline-text">I worked as IT Support Assistant at <a href="https://www.unitn.it">UniTn</a></p>
        </div>
        <div class="timeline-item minor">
            <p class="timeline-date">July 2021</p>
            <p class="timeline-text">First release of the <a href="https://addons.mozilla.org/it/firefox/addon/websites-notes/">Notefox</a> add-on</p>
        </div>
        <div class="timeline-item minor">
            <p class="timeline-date">2021 &mdash; 2026</p>
            <p class="timeline-text">I became a volunteer of <a href="https://cri.it">Croce Rossa Italiana</a></p>
        </div>
        <div class="timeline-item minor">
            <p class="timeline-date">May 2021</p>
            <p class="timeline-text">First release of the <a href="https://addons.mozilla.org/it/firefox/addon/limite/">Limite</a> add-on</p>
        </div>
        <div class="timeline-item minor">
            <p class="timeline-date">January 2021</p>
            <p class="timeline-text">First release (alpha) of <a href="https://play.google.com/store/apps/details?id=com.saverio.wordoftheday_en">Word of the Day</a></p>
        </div>
        <div class="timeline-item minor">
            <p class="timeline-date">January 2021</p>
            <p class="timeline-text">First release of <a href="https://play.google.com/store/apps/details?id=com.saverio.pdfviewer">Sav PDF Viewer Pro</a></p>
        </div>
        <div class="timeline-item minor">
            <p class="timeline-date">October 2020 &mdash; December 2023</p>
            <p class="timeline-text">I was a volunteer of <a href="https://www.informaticisenzafrontiere.org/en/">Informatici Senza Frontiere</a></p>
        </div>
        <div class="timeline-item">
            <span class="timeline-badge badge-volunteering">Volunteering</span>
            <p class="timeline-date">March 2020</p>
            <p class="timeline-text">I became a <a href="https://people.mozilla.org/p/Sav22999">Mozilla Rep</a></p>
        </div>
        <div class="timeline-item minor">
            <p class="timeline-date">November 2019</p>
            <p class="timeline-text">Published the <a href="https://www.saveriomorelli.com/htmlpertutti/">HTML per tutti</a> book</p>
        </div>
        <div class="timeline-item minor">
            <p class="timeline-date">November 2019</p>
            <p class="timeline-text">First release of the <a href="https://addons.mozilla.org/it/firefox/addon/emoji-sav/">Emoji</a> add-on</p>
        </div>
        <div class="timeline-item minor">
            <p class="timeline-date">November 2019 &mdash; July 2026</p>
            <p class="timeline-text">First release (alpha) of the <a href="https://www.saveriomorelli.com/commonvoice/">CV Project</a> app</p>
        </div>
        <div class="timeline-item">
            <span class="timeline-badge badge-education">Education</span>
            <p class="timeline-date">September 2019</p>
            <p class="timeline-text">Started University in Trento (B.Sc. Computer Science) at <a href="https://www.unitn.it/en">UniTn</a></p>
        </div>
        <div class="timeline-item minor">
            <p class="timeline-date">April 2019</p>
            <p class="timeline-text">First release of the <a href="https://addons.mozilla.org/it/firefox/addon/accented-letters/">Accented Letters</a> add-on</p>
        </div>
        <div class="timeline-item minor">
            <p class="timeline-date">September 2018</p>
            <p class="timeline-text">First release of <a href="https://t.me/mozitabot">MozItaBot</a></p>
        </div>
        <div class="timeline-item">
            <span class="timeline-badge badge-other">Other</span>
            <p class="timeline-date">September 2018</p>
            <p class="timeline-text">I became a swimming teacher (FIN)</p>
        </div>
        <div class="timeline-item">
            <span class="timeline-badge badge-education">Education</span>
            <p class="timeline-date">July 2018</p>
            <p class="timeline-text">Finished High School (A-level)</p>
        </div>
        <div class="timeline-item minor">
            <p class="timeline-date">2018 &mdash; 2020</p>
            <p class="timeline-text">First release of <a href="https://sav22999.github.io/project_nolimit_math/">NoLimit Math</a></p>
        </div>
        <div class="timeline-item">
            <span class="timeline-badge badge-volunteering">Volunteering</span>
            <p class="timeline-date">September 2017</p>
            <p class="timeline-text">I became a volunteer of <a href="https://mozillaitalia.org">Mozilla Italia</a></p>
        </div>
        <div class="timeline-item minor">
            <p class="timeline-date">2011 &mdash; 2020</p>
            <p class="timeline-text">First release of <a href="https://github.com/Sav22999/mycodeeditor">My Code Editor</a></p>
        </div>
        <div class="timeline-item minor">
            <p class="timeline-date">2004</p>
            <p class="timeline-text">My love of swimming has begun</p>
        </div>
        <div class="timeline-item">
            <p class="timeline-date">22 September 1999</p>
            <p class="timeline-text">I was born in Tricarico, <a href="https://www.regione.basilicata.it/">Basilicata</a></p>
        </div>
    </div>
</section>

<?php include_once(dirname(__DIR__) . '/include/footer.php'); ?>

</body>
</html>
