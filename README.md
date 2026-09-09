# Tuinman Piet Portfolio Website

## Projectomschrijving

Dit project is een maatwerk PHP/MySQL website voor Tuinman Piet.

De website is bedoeld als professioneel online visitekaartje en portfolio. Bezoekers kunnen zien wie Tuinman Piet is, welke werkzaamheden hij uitvoert en welke projecten/klussen hij heeft gedaan. Tuinman Piet kan via een afgeschermde beheeromgeving zelf projecten en foto’s toevoegen.

De website is gebouwd zonder WordPress, omdat er gekozen is voor een eigen lichte oplossing met HTML, CSS, JavaScript, PHP en MySQL.

---

## Doel van de website

De website moet:

- Tuinman Piet professioneel presenteren.
- Zijn werkzaamheden duidelijk tonen.
- Projecten/klussen met foto’s laten zien.
- Bezoekers snel laten bellen of WhatsAppen.
- Piet zelf projecten en foto’s laten beheren.
- Bezoekers geen toegang geven tot upload- of beheerfuncties.

---

## Gebruikte technieken

Frontend:

- HTML
- CSS
- JavaScript

Backend:

- PHP

Database:

- MySQL / MariaDB

Lokale ontwikkelomgeving:

- Laragon
- HeidiSQL

Hostingdoel:

- Strato

---

## Projectstructuur

```text
tuinmanpiet/
├── index.php
├── projecten.php
├── project.php
├── README.md
│
├── admin/
│   ├── login.php
│   ├── dashboard.php
│   ├── project-toevoegen.php
│   ├── projecten-beheren.php
│   ├── project-bewerken.php
│   └── logout.php
│
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── script.js
│   └── img/
│       └── logo.png
│
├── database/
│   └── setup.sql
│
├── includes/
│   ├── db.php
│   ├── auth.php
│   ├── header.php
│   └── footer.php
│
└── uploads/
    └── projects/
```
