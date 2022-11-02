# BliSynlig AS - Under konstruksjon

Dette er en plugin skreddersydd for BliSynlig AS-

## Installasjon

Last ned .zip filen og ekstrakt mappen med navn "blisynlig-konstruksjon".
Inni denne mappen bør det ligge en mappe til med navn "languages", og en .php fil som heter "blisynlig-konstruksjon.php". Hvis disse filene ikke ligger der, prøv å last ned på nytt, eller få mappen fra et annet sted.


## Bruk

Når denne pluginen er aktivert, har man mulighet til å aktivere, og skreddersy en egen "under kontstruksjon" side med HTML markup.
- Aktiver 'Under-kontruksjon'- siden. ~ Når denne er huket av, vil 'Under kontrsuksjon' siden være aktivert.
- Gjør Wordpress sin static hjemmeside synlig: 'sidenavn' ~ Denne vil gjøre din en av dine sider synlig. Dette må være en statisk nettside, uten muilghet for bruker å komme seg videre.
- HTML som vises på 'Under-konstruksjon'- siden ~ Her skriver du HTML markup som vil vises hvis man prøver å gå inn på nettstedet som er under konstruksjon. Dette kan være så komplisert som mulig, eller bare litt tekst.
- Hemmelig ord for å omgå for én nettleser ~ Hvis et ord et skrevet inn, vil dette være et hemmelig ord som kan brukes for å fortsatt se nettstedet. Dette ordet kan bare brukes for én nettleser. Brukeren som skal se nettstedet kan legge til "?hemmeligord" på slutten av URL'en for å komme inn. Dette vil da bli lagret som en cookie i brukeren sin nettleser, slik at denne brukeren blir husket, og kan gå inn og ut osm den selv vil, helt til det hemmelige ordet blir fjernet eller byttet.
- Angi antall dager siden skal huskes av nettleseren ~ Her kan du angi hvor mange dager brukeren som bruker det hemmelige ordet, skal bli husket av nettleseren, før cookien blir fjernet. Standarden er 30 dager om ikke annet er oppgit, og maksen en 365 dager.
- Bruker-IP-adresser til whitelist ~ Her kan legge til bruker-IP til whitelisten. Én IP per rad. Man kan kommentere etter IP-en for å lettere huske hvilken bruker eller tjeneste som bruker IP-en. Vi finner den første IP-adressen ved hver nye rad, så du kan lettere kommentere. Brukere som har IP-adressen som blir skrevet inn, vil kunne komme in på, og se nettstedet ditt, selvom 'under konstruksjon' er aktivert.

## Standard 'under konstruksjon'- side for BliSynlig AS
Dette er en standard 'under konstruksjon'- side for BliSynlig AS sin nettside. Denne siden har BliSynlig AS sin .png logo på toppen, men teksten "Denne siden er for øyeblikket under konstruksjon", "For henvendelser, kontakt oss her:", forfulgt av e-post og telefon ikoner, med klikkbare lenker til epost og telefon.

Her kan selvfølgelig alt endres på. CSS, HTML, og kan også legge til JavaScript med en <script> tag. Denne siden kan da endres på til andre kunder, ved å bytte farger, logoer, tekster, osv.
```html
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap');

    * {
        text-align: center;
    }

    .main {
        width: max-content;
        height: min-content;
        padding: 50px;
        position: absolute;
    }

    * {
        margin: 0;
        padding: 0;
    }

    body {
        display: flex;
        flex-direction: column;
        background-color: #bfdac6;
        justify-content: center;
        align-items: center;
        font-family: 'Poppins', sans-serif;
    }

    a {
        text-decoration: none;
        color: #2C5B32;
        line-height: 24px;
        transition: .3s;
    }

    a:hover {
        color: #59B463;
        transform: scale(1.2);
        font-size: 18px;
        transition: .3s;
    }

    h3 {
        font-size: 18.72px;
        line-height: 24px;
    }
</style>

<div class="main">
    <img src="https://i.imgur.com/IMABxA5.png" style="width:400px">
    <br><br>
    <h3>Denne siden er for øyeblikket under konstruksjon</h3>
    <h3>For henvendelser, kontakt oss her:</h3>
    <br>
    <div class="contact">
        <img src="https://cdn-icons-png.flaticon.com/512/561/561127.png" style="height:50px">
        <br>
        <a href="#">hei@blisynlig.no</a>
        <br><br><br>
        <img src="https://cdn2.iconfinder.com/data/icons/font-awesome/1792/phone-512.png" style="height:50px">
        <br>
        <a href="#">12 34 56 78</a>
    </div>
</div>
<br><br>
```

## License
[GPLv3.0](https://choosealicense.com/licenses/gpl-3.0/#)