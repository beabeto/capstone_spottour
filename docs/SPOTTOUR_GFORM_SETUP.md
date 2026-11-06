# SpotTour — Ready Google Form (Apps Script)

## Mahalagang paalala / Important note

**Hindi** kayang gumawa ng Cursor / AI ng Google Form *sa loob mismo* ng Gmail o Google account mo — kailangan ng login mo.  
Ang script sa `docs/spottour-evaluation-gform-apps-script.js` ang pinakamalapit sa “ready GForm”: i-paste mo, i-Run, at **~1 minuto** ay may live form na sa Google Forms account mo.

Cursor cannot create the form inside your Google account for you. This Apps Script builds the real ready Google Form in your account in about one minute.

---

## Hakbang-hakbang (Filipino)

1. Buksan ang [https://script.google.com](https://script.google.com) → **New project**.
2. Burahin ang default code. Buksan ang file na `docs/spottour-evaluation-gform-apps-script.js` sa project, **kopyahin lahat**, tapos i-paste sa Script Editor. I-save (hal. pangalan: `SpotTour Survey`).
3. Sa dropdown ng function, piliin ang **`createSpotTourEvaluationForm`**, tapos i-click ang **Run**.
4. Kapag humingi ng permission: **Review permissions** → piliin ang Google account mo → **Allow** (kailangan para makagawa ng Form).
5. Pagkatapos tumakbo: **View → Logs** (o Execution log). Makikita mo ang:
   - **Edit URL** — para i-edit / tingnan ang responses
   - **Published response URL** — ito ang i-share sa respondents

Optional: sa Google Forms, i-click **Send** para magpadala ng link o QR code.

---

## Steps (English, brief)

1. Open [script.google.com](https://script.google.com) → **New project**.
2. Paste the entire contents of `docs/spottour-evaluation-gform-apps-script.js`.
3. Run **`createSpotTourEvaluationForm`**.
4. Authorize the script when prompted.
5. Open **View → Logs** and copy the live form links.

---

## Ano ang laman ng form?

- Section 1: Demographics (optional name, age, gender, role, tourism-site usage)
- Sections 2–6: Likert 1–5 (Functional Suitability, Usability, Reliability/Performance, Security, Usefulness/Tourism Impact)
- Section 7: Overall satisfaction + open feedback

Kung gusto mong muling gumawa ng bagong kopya ng form, i-Run ulit ang function — may bagong Form ang gagawin sa Drive/Forms mo.
