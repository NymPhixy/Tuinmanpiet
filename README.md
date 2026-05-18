# Tuinman Piet - Website met Veilige Portfolio-Beheerstructuur

## 📋 Inhoud

- [🎯 Projectdoel](#-projectdoel)
- [🏗️ Architectuur](#️-architectuur)
- [📁 Mappenstructuur](#-mappenstructuur)
- [🚀 Starten](//#-starten)
- [👁️ Publieke Website](#️-publieke-website)
- [🔐 Admin-omgeving & Decap CMS](#-admin-omgeving--decap-cms)
- [🛡️ Beveiligingsmodel](#️-beveiligingsmodel)
- [💾 Data-structuur](#-data-structuur)
- [🌐 Live Deployment](#-live-deployment)
- [🚀 Volgende Stappen](#-volgende-stappen)

---

## 🎯 Projectdoel

Website voor **Tuinman Piet** uit Musselkanaal:

- ✅ **Bezoekers** kunnen projecten bekijken (lezen-alleen)
- ✅ **Piet** kan projecten en foto's beheren via veilige admin-omgeving
- ✅ **Geen** openbare uploadfunctionaliteit op de website
- ✅ **Volledig responsive** en toegankelijk
- ✅ **Voorbereid** voor live deployment met Decap CMS

---

## 🏗️ Architectuur

### Overzicht

```
┌─────────────────────────────────────┐
│      PUBLIEKE WEBSITE               │
│  (index.html, projecten.html)       │
│                                     │
│  • Leest projecten uit JSON         │
│  • Toont portfolio                  │
│  • GEEN uploadfunctionaliteit       │
└────────────┬────────────────────────┘
             │
             ├─► /data/projects.json
             │   (data-driven)
             │
             └─► /assets/img/projects/
                 (foto's)

┌─────────────────────────────────────┐
│      ADMIN-OMGEVING                 │
│  (/admin/index.html)                │
│                                     │
│  • Decap CMS interface              │
│  • Beveiligd (GitHub login)         │
│  • Volledige beheerrechten          │
│  • LIVE functionaliteit na hosting  │
└─────────────────────────────────────┘
```

---

## 📁 Mappenstructuur

```
tuinmanpiet/
├── index.html                   # Homepage
├── projecten.html               # Projectenpagina
├── style.css                    # Styling (groen/oranje)
├── script.js                    # JavaScript (modaal, filters)
├── README.md                    # Deze file
│
├── data/
│   └── projects.json            # Projectdata (JSON)
│
├── assets/
│   └── img/
│       ├── projects/            # Projectfoto's
│       ├── logo.png             # Logo (homepage & navbar)
│       └── icon.png             # Favicon
│
└── admin/
    ├── index.html               # Decap CMS interface
    └── config.yml               # Decap CMS configuratie
```

---

## 🚀 Starten

### Lokaal testen met Live Server

1. **Open Live Server**
   - VS Code: Installeer "Live Server" extension
   - Klik rechts op `index.html` → "Open with Live Server"
   - Of: Zelf draaiende server op poort 5500

2. **Test de website**
   - Homepage: `http://localhost:5500`
   - Projectenpagina: `http://localhost:5500/projecten.html`
   - Admin (niet volledig functioneel): `http://localhost:5500/admin`

3. **Projectenpagina features testen**
   - ✅ Filter op categorie (Alles, Aanleg, Onderhoud, etc.)
   - ✅ Klik op projectkaart → modal opent
   - ✅ Modal sluiten: sluitknop, Escape-toets, of buiten klick
   - ✅ Extra foto's zichtbaar in modal

---

## 👁️ Publieke Website

### Wat bezoekers zien

#### Homepage (`index.html`)

- Logo en navigatie
- Hero-sectie met CTA buttons (Bel/WhatsApp)
- Werkzaamheden overzicht (iconen met beschrijvingen)
- Portfolio preview (eerste 3 projecten)
- Over Piet sectie
- Contact informatie

#### Projectenpagina (`projecten.html`)

- Volledige projectenportfolio
- **Filter buttons**: Alles, Aanleg, Onderhoud, Renovatie, Bestrating, Schuttingen
- **Responsive grid** met projectkaarten
- **Modal/Lightbox** bij klikken op project
  - Grote afbeelding
  - Titel, categorie, locatie, jaar
  - Beschrijving
  - Extra foto's (carousel-stijl)
  - Sluitopties: knop, Escape, buiten-klick

### Beveiligingsfeatures

#### Wat NIET beschikbaar is voor bezoekers:

- ❌ Geen uploadknop
- ❌ Geen bewerkknoppen
- ❌ Geen verwijderknoppen
- ❌ Geen adminpaneel links
- ❌ Geen wachtwoordvelden
- ❌ Geen formulieren voor wijzigingen

#### Foutafhandelingen

- Nette boodschap als `projects.json` niet laadt
- Loading-indicator terwijl projecten laden
- Fallback-afbeeldingen (placeholder)

---

## 🔐 Admin-omgeving & Decap CMS

### Wat is Decap CMS?

Decap CMS (voorheen NetlifyCMS) is een **content management system** dat werkt met GitHub:

- Geen aparte server nodig
- Git-gebaseerd (alles wordt opgeslagen in GitHub)
- Eenvoudig web-interface (`/admin`)
- Ideaal voor statische sites

### Admin Features (na live deployment)

**Piet kan via `/admin` inloggen en:**

- ✅ Projecten toevoegen (titel, categorie, foto's, etc.)
- ✅ Projecten bewerken (alles aanpassen)
- ✅ Projecten verwijderen
- ✅ Foto's uploaden centraal beheer
- ✅ Alle wijzigingen direct live

**Veiligheid:**

- 🔒 Alleen Piet kan inloggen (GitHub-gebaseerd)
- 🔒 Alle wijzigingen zijn traceerbaar in Git
- 🔒 Geen directe toegang tot bestanden nodig

### Admin Interface

```
/admin/
├── index.html       → Decap CMS web-interface
└── config.yml       → Configuratie (velden, folders, etc.)
```

#### config.yml Inhoud

```yaml
backend:
  name: git-gateway # Git-gebaseerde backend (Netlify)
  branch: main

collections:
  - name: "projects"
    fields:
      - title
      - category (select: Aanleg, Onderhoud, ...)
      - location
      - year
      - description
      - coverImage (foto-upload)
      - images (extra foto's)
```

---

## 🛡️ Beveiligingsmodel

### Publieke Website Security

**Bezoekers kunnen NIET:**

- Uploads doen
- Projecten bewerken
- Projecten verwijderen
- Inloggen
- Toegang tot admin krijgen

**Implementatie:**

- Geen `<input type="file">` op publieke pagina's
- Geen bewerk-formulieren
- Geen admin-links
- Data uitsluitend ingelezen uit JSON (lezen-alleen)

### Admin Security (Decap CMS)

**Piet (admin) kan:**

- Inloggen met GitHub account
- Projecten volledig beheren
- Foto's uploaden

**Bescherming:**

- GitHub OAuth-authenticatie
- Alleen uitgenodigde GitHub-gebruikers hebben toegang
- Netlify Identity controleert authentication
- Alle wijzigingen gaan naar GitHub (audit trail)
- No direct file access nodig

### Data Security

- `projects.json`: geen gevoelige data
- `assets/img/`: alleen foto's
- Geen gebruikersgegevens of email-adressen in code

---

## 💾 Data-structuur

### projects.json

Formaat: **Root object met `projects` array**

```json
{
  "projects": [
    {
      "id": 1,
      "title": "Tuinonderhoud in Musselkanaal",
      "category": "Onderhoud",
      "location": "Musselkanaal",
      "year": "2026",
      "description": "...",
      "coverImage": "assets/img/projects/project-1.jpg",
      "images": [
        "assets/img/projects/project-1.jpg",
        "assets/img/projects/project-1-detail.jpg"
      ]
    }
    // ... meer projecten
  ]
}
```

### Script.js Data-handling

Script ondersteunt BEIDE structuren:

- ✅ Root array: `[{...}, {...}]`
- ✅ Root object: `{ projects: [{...}, {...}] }`

```javascript
// Automatische normalisatie:
function normalizeProjects(data) {
  if (Array.isArray(data)) return data;
  if (data?.projects) return data.projects;
  return [];
}
```

---

## 🌐 Live Deployment

### Fase 1: Voorbereiding (NU)

✅ Website is klaar:

- Publieke pagina's met projectportfolio
- Admin-omgeving voorbereid
- Geen uploadfunctionaliteit op publiek
- Veilig voor live gebruik

### Fase 2: Live zetten (Volgende stap)

**Als Piet website live wil zetten:**

1. **GitHub Repository**

   ```bash
   git push origin main
   ```

2. **Netlify Deployment**
   - Ga naar https://app.netlify.com
   - Klik "New site from Git"
   - Selecteer GitHub repo
   - Deploy! (automatisch bij elke push)

3. **Netlify Identity**
   - Enable "Identity" in Netlify dashboard
   - Voeg Piet toe als gebruiker
   - Piet krijgt uitnodigingsmail

4. **Git Gateway**
   - Enable "Git Gateway" in Identity settings
   - Nu kan Piet projecten via `/admin` beheren

5. **Waar is de site?**
   - Public: `https://tuinmanpiet.netlify.app`
   - Admin: `https://tuinmanpiet.netlify.app/admin`

### Kostenloze opties:

- ✅ Netlify (hosting): Gratis
- ✅ GitHub (repo): Gratis
- ✅ Decap CMS: Gratis/open-source
- ✅ Domain: `*.netlify.app` gratis (eigen domain ~€10/jaar)

---

## 🚀 Volgende Stappen

### Voor Piet:

1. **Website Live Zetten**
   - Samen GitHub & Netlify koppelen
   - ~30 minuten werk
   - Result: site live, admin werkt

2. **Custom Domain** (optioneel)
   - Eigen domein kopen (bijv. `tuinmanpiet.nl`)
   - Koppelen aan Netlify
   - Kosten: ~€10-15/jaar

3. **Admin Gebruiken**
   - Inloggen op `/admin`
   - Projecten toevoegen/bewerken
   - Feedback geven → wijzigingen doorvoeren

### Voor Developers/Technicus:

**Te doen:**

1. Code naar GitHub pushen
2. Netlify project aanmaken
3. Netlify Identity + Git Gateway configureren
4. Piet als admin toevoegen
5. Testen of alles werkt

**Als iets aanpassing nodig:**

1. Wijzigingen maken in code/config
2. Push naar GitHub
3. Netlify deployed automatisch
4. Check `/admin` → config.yml aanpassingen worden geladen

---

## 📚 Technische Details

### Frontend Frameworks

- **HTML5**: Semantic markup, accessibility
- **CSS3**: Responsive design, CSS variables
- **JavaScript (Vanilla)**: Geen frameworks nodig!
  - Hamburger menu (mobiel)
  - Project filtering
  - Modal/lightbox
  - JSON data loading

### CMS Framework

- **Decap CMS**: Headless CMS met Git backend
- **Backend**: Git Gateway (Netlify)
- **Format**: JSON (projects.json)
- **Media**: Netlify CDN (auto-uploads)

### Browser Support

- ✅ Chrome/Edge (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Mobile browsers (iOS/Android)

### Accessibility (WCAG 2.1 AA)

- ✅ Semantic HTML
- ✅ Alt-teksten op afbeeldingen
- ✅ Aria-labels waar nodig
- ✅ Keyboard navigation
- ✅ Focus states (outline)
- ✅ Color contrast (WCAG)
- ✅ Touch-friendly knoppen (min. 44px)

---

## 🎨 Ontwerp & Styling

### Kleurenschema

- **Primair**: Oranje `#ff9800` (CTA buttons, accenten)
- **Secundair**: Groen `#4caf50` (labels, highlights)
- **Achtergrond**: Wit `#ffffff`
- **Tekst**: Donkergrijs `#333333`

### Responsive Breakpoints

- 📱 Mobile: `< 480px`
- 📱 Tablet: `480px - 768px`
- 💻 Desktop: `> 768px`

---

## 📞 Vragen & Support

### Voor Piet:

- Website werkt lokaal ✅
- Kan je zien hoe het eruit ziet ✅
- Admin werkt lokaal (preview-only) ✅
- Volledige CMS-functionaliteit: na live deployment

### Voor meer info:

- Decap CMS docs: https://decapcms.org/docs/
- Netlify docs: https://docs.netlify.com/
- GitHub Pages: https://pages.github.com/

---

## 📄 Summary

**Wat hebben we nu:**

- ✅ Veilige publieke website zonder uploads
- ✅ Projectportfolio met filters & modal
- ✅ Admin-omgeving voorbereid
- ✅ Data-driven (JSON)
- ✅ Responsive & accessible
- ✅ Standaard webstandaarden

**Wat werkt nog niet:**

- ⏳ Admin save/publish (geen backend lokaal)
- ⏳ Foto-uploads (vereist hosting)

**Wat werkt straks na hosting:**

- ✅ Piet kan inloggen op `/admin`
- ✅ Piet kan projecten toevoegen/wijzigen
- ✅ Alles gaat automatisch live
- ✅ Volledige portfolio-beheer mogelijk

---

Made with ❤️ for Tuinman Piet | Gemaakt door RGB Visuals
