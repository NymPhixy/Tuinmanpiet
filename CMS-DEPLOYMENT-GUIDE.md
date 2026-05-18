# 🌱 Tuinman Piet Website - CMS Setup Gids

## 📋 Overzicht

Deze website is gebouwd met HTML, CSS en JavaScript, maar is voorgeprepared voor een **Content Management System (CMS)**. Dit betekent dat Piet **zelf projecten kan toevoegen, wijzigen en verwijderen** via een eenvoudige web-interface, zonder code te hoeven aanpassen.

---

## 🚀 Voor Starters: Lokaal Testen

### Website lokaal runnen

1. Open de map in VS Code
2. Installeer **Live Server** extension
3. Rechts-klik op `index.html` → "Open with Live Server"
4. Website opent op `http://localhost:5500`

**De projectenpagina werkt al!** Projecten wordt geladen uit `/data/projects.json`

---

## 🌍 Voor Live Deployment met CMS

**Dit is wat Ruben moet doen:**

### Stap 1: Repository op GitHub

```bash
# Zorg dat alle website-bestanden in een GitHub repo staan
git init
git add .
git commit -m "Tuinman Piet website met CMS voorbereiding"
git remote add origin https://github.com/jouw-username/tuinmanpiet.git
git branch -M main
git push -u origin main
```

**Link:** https://github.com/new (maak repository aan)

---

### Stap 2: Deploy via Netlify

1. Ga naar https://netlify.com
2. Klik **"Add new site"**
3. Kies **"Connect to Git"**
4. Selecteer **GitHub**
5. Selecteer de `tuinmanpiet` repository
6. Instellingen:
   - **Build command:** (leeg laten)
   - **Publish directory:** `.` (root folder)
7. Klik **Deploy site**

Je krijgt automatisch een URL zoals:

```
https://tuinmanpiet-xyz123.netlify.app
```

**Deze URL kun je straks veranderen naar je eigen domein!**

---

### Stap 3: Identity & Git Gateway Activeren

1. In Netlify Dashboard → Kies je site
2. Ga naar **Site settings** → **Identity**
3. Klik **Enable Identity**
4. Scroll naar beneden
5. Klik **Enable Git Gateway**

Nu werkt de CMS!

---

### Stap 4: Gebruikers Uitnodigen (Piet)

1. Netlify → Kies je site → **Identity** → **Users**
2. Klik **Invite users**
3. Voer Piets email in
4. Piet ontvangt een uitnodigingsmail
5. Piet klikt op de link en stelt een wachtwoord in

---

### Stap 5: CMS Live Brengen

De CMS is nu beschikbaar op:

```
https://jouw-netlify-site.netlify.app/admin/
```

Piet kan hier inloggen en:

- ✅ Projecten toevoegen
- ✅ Projecten wijzigen
- ✅ Projecten verwijderen
- ✅ Foto's uploaden

Alles wordt automatisch opgeslagen en live gezet!

---

## 📁 Bestandsstructuur Uitgelegd

```
tuinmanpiet/
├── index.html                    # Homepage
├── projecten.html                # Projectenpagina
├── style.css                     # Alle stijlen
├── script.js                     # JavaScript (menu, filters, modal)
│
├── data/
│   └── projects.json             # Project data (laadt automatisch)
│
├── admin/
│   ├── index.html                # CMS interface
│   └── config.yml                # CMS configuratie
│
├── assets/
│   └── img/
│       └── projects/             # Plaats voor projectfoto's
│
└── .git/                         # Git repository (na eerste commit)
```

---

## 🎯 Workflow: Hoe Piet Gebruik Maakt

### 1️⃣ Inloggen

```
https://www.tuinmanpiet.nl/admin/
↓
E-mailadres + wachtwoord
```

### 2️⃣ Nieuw Project Toevoegen

1. CMS interface opent
2. Klik **"Nieuw project"**
3. Vul in:
   - **Titel:** "Tuinaanleg in Musselkanaal"
   - **Categorie:** Kies uit dropdown (Aanleg, Onderhoud, etc.)
   - **Locatie:** "Musselkanaal"
   - **Jaar:** "2026"
   - **Omschrijving:** Korte tekst over het project
   - **Voorafbeelding:** Klik → foto uploaden
   - **Extra afbeeldingen:** Voeg meerdere foto's toe
4. Klik **"Publish"**
5. Website update automatisch! 🎉

### 3️⃣ Project Wijzigen

1. Klik op het project in de CMS
2. Edit de velden
3. Klik **"Publish"**
4. Live!

### 4️⃣ Project Verwijderen

1. Klik op het project
2. Klik **"Delete"**
3. Bevestig
4. Weg is het project

---

## 🔐 Beveiliging

- **Email-based login** (alleen Piet kan inloggen met zijn email)
- **GitHub integration** (alles is versioned, je kunt wijzigingen terugdraaien)
- **Automatic backups** (Netlify maakt automatisch backups)
- **HTTPS** (gratis SSL-certificaat via Netlify)

---

## 💰 Kosten

| Service    | Kosten | Waarom?               |
| ---------- | ------ | --------------------- |
| GitHub     | Gratis | Versiecontrol         |
| Netlify    | Gratis | Hosting + CMS backend |
| Decap CMS  | Gratis | Open source CMS       |
| **Totaal** | **€0** | Volledige setup!      |

---

## ⚠️ Dingen om te Onthouden

✅ **Wel mogelijk:**

- Projecten CRUD (Create, Read, Update, Delete)
- Automatische deployment
- Backup & versieringsverlies

❌ **Niet inbegrepen (kan later):**

- Geavanceerde SEO tools
- E-commerce / betaling
- Advanced analytics
- Custom domains (.nl/.com) → dit kost jaarlijks ~€10-15

---

## 🛠️ Troubleshooting

### "Ik zie de CMS niet op /admin/"

→ Zorg dat **Identity** en **Git Gateway** zijn enabled in Netlify

### "Website update niet na wijziging in CMS"

→ Wacht 30 seconden, Netlify bouwt automatisch op
→ Check Netlify Deploys tab voor status

### "Foto's uploaden werkt niet"

→ Zorg dat `/assets/img/projects/` folder bestaat

### "Projecten laden niet op de site"

→ Zorg dat `data/projects.json` correct geformatted is
→ Open DevTools (F12) → Console tab → controleer errors

---

## 📚 Nuttige Links

- **Netlify Docs:** https://docs.netlify.com
- **Decap CMS Docs:** https://decapcms.org
- **Git Basics:** https://git-scm.com/doc

---

## 🎓 Volgende Stappen (Toekomst)

Mogelijke uitbreidingen:

1. **Contactformulier** (met email notificatie)
2. **Reviews/Testimonialen** (van klanten)
3. **Blog** (Piet kan berichten posten)
4. **Appointment booking** (klanten boeken online)
5. **Photo gallery** (Pinterest-style fotobrowser)
6. **SEO optimalisatie** (Google ranking)

---

## ✉️ Contact Ruben

Voor tech support of vragen over setup:

- Email: (voeg je contact info in)
- Telefoon: (voeg je telefoonnummer in)

---

**Veel succes met Tuinman Piet online! 🌿**
