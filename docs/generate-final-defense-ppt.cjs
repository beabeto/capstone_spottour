/**
 * SpotTour Final Defense PowerPoint generator
 * Run: node docs/generate-final-defense-ppt.cjs
 */
const PptxGenJS = require('pptxgenjs');
const path = require('path');

const pptx = new PptxGenJS();
pptx.defineLayout({ name: 'WIDE', width: 13.333, height: 7.5 });
pptx.layout = 'WIDE';
pptx.author = 'Ianzel A. Morales & Beatrix Coleen Beto';
pptx.title = 'SpotTour Final Defense';
pptx.subject = 'Final Defense Presentation — SpotTour';

const C = {
  teal: '0F766E',
  tealDark: '134E4A',
  tealSoft: 'CCFBF1',
  tealMid: '14B8A6',
  cream: 'F8FAF9',
  white: 'FFFFFF',
  ink: '1F2937',
  muted: '4B5563',
  line: 'D1D5DB',
  softBg: 'ECFDF5',
  warnBg: 'FEF3C7',
  warnInk: '92400E',
};

function addFooter(slide, page, total) {
  slide.addShape(pptx.shapes.RECTANGLE, {
    x: 0, y: 7.15, w: 13.333, h: 0.35,
    fill: { color: C.tealDark },
  });
  slide.addText('SpotTour  ·  Final Defense  ·  SNSU – CCIS', {
    x: 0.4, y: 7.18, w: 10, h: 0.28,
    fontSize: 10, color: 'A7F3D0', fontFace: 'Calibri',
  });
  slide.addText(`${page} / ${total}`, {
    x: 11.5, y: 7.18, w: 1.4, h: 0.28,
    fontSize: 10, color: 'A7F3D0', fontFace: 'Calibri', align: 'right',
  });
}

function sectionBanner(slide, label) {
  slide.addShape(pptx.shapes.ROUNDED_RECTANGLE, {
    x: 0.45, y: 0.28, w: 3.4, h: 0.36,
    fill: { color: C.tealSoft }, rectRadius: 0.08,
  });
  slide.addText(label, {
    x: 0.45, y: 0.28, w: 3.4, h: 0.36,
    fontSize: 11, color: C.tealDark, fontFace: 'Calibri', bold: true,
    align: 'center', valign: 'middle',
  });
}

function slideTitle(slide, title) {
  slide.addText(title, {
    x: 0.45, y: 0.75, w: 12.4, h: 0.55,
    fontSize: 28, color: C.tealDark, fontFace: 'Calibri', bold: true,
  });
}

function bullets(slide, items, opts = {}) {
  const x = opts.x ?? 0.55;
  const y = opts.y ?? 1.5;
  const w = opts.w ?? 12.2;
  const fontSize = opts.fontSize ?? 16;
  slide.addText(
    items.map((t) => ({
      text: t,
      options: { bullet: true, breakLine: true, color: C.ink, fontSize, fontFace: 'Calibri', paraSpaceAfter: 8 },
    })),
    { x, y, w, h: opts.h ?? 5.2, valign: 'top' }
  );
}

const TOTAL = 16;

// 1. TITLE
{
  const s = pptx.addSlide();
  s.addShape(pptx.shapes.RECTANGLE, { x: 0, y: 0, w: 13.333, h: 7.5, fill: { color: C.tealDark } });
  s.addShape(pptx.shapes.RECTANGLE, { x: 0, y: 0, w: 0.22, h: 7.5, fill: { color: C.tealMid } });
  s.addText('FINAL DEFENSE', {
    x: 0.7, y: 1.35, w: 12, h: 0.35,
    fontSize: 14, color: '99F6E4', fontFace: 'Calibri', bold: true, charSpacing: 3,
  });
  s.addText('SPOTTOUR: An Interactive Platform for\nTourism and Community Engagement', {
    x: 0.7, y: 1.85, w: 11.5, h: 1.5,
    fontSize: 32, color: C.white, fontFace: 'Calibri', bold: true,
  });
  s.addText('A Capstone Project Presented to the Faculty of the\nCollege of Computing & Information Sciences\nSurigao del Norte State University', {
    x: 0.7, y: 3.55, w: 11, h: 1.0,
    fontSize: 15, color: 'CCFBF1', fontFace: 'Calibri',
  });
  s.addText('Ianzel A. Morales  ·  Beatrix Coleen Beto', {
    x: 0.7, y: 4.85, w: 11, h: 0.4,
    fontSize: 18, color: C.white, fontFace: 'Calibri', bold: true,
  });
  s.addText('Bachelor of Science in Information Technology  ·  May 2026', {
    x: 0.7, y: 5.35, w: 11, h: 0.35,
    fontSize: 14, color: '99F6E4', fontFace: 'Calibri',
  });
}

// 2. TEAM
{
  const s = pptx.addSlide();
  s.addShape(pptx.shapes.RECTANGLE, { x: 0, y: 0, w: 13.333, h: 7.5, fill: { color: C.cream } });
  sectionBanner(s, '1. INTRODUCTION');
  slideTitle(s, 'Introduction of the Team');
  const people = [
    { role: 'Researchers', lines: 'Ianzel A. Morales\n\nBeatrix Coleen Beto' },
    { role: 'Subject Instructor', lines: 'Ms. Alma Christie Reyna' },
    { role: 'Technical Adviser', lines: 'Engr. Ritchie A. Reyna' },
    { role: 'Tourism Guidance', lines: 'Mr. Rayciel Beto\n\nMr. Glenn Bandojo' },
  ];
  people.forEach((p, i) => {
    const x = 0.45 + i * 3.15;
    s.addShape(pptx.shapes.ROUNDED_RECTANGLE, {
      x, y: 1.7, w: 3.0, h: 3.6,
      fill: { color: C.white }, rectRadius: 0.12,
      shadow: { type: 'outer', color: '000000', blur: 8, opacity: 0.08, offset: 2 },
    });
    s.addShape(pptx.shapes.RECTANGLE, { x, y: 1.7, w: 3.0, h: 0.7, fill: { color: C.teal } });
    s.addText(p.role, {
      x, y: 1.85, w: 3.0, h: 0.4,
      fontSize: 13, color: C.white, fontFace: 'Calibri', bold: true, align: 'center',
    });
    s.addText(p.lines, {
      x: x + 0.2, y: 2.9, w: 2.6, h: 2.0,
      fontSize: 15, color: C.ink, fontFace: 'Calibri', align: 'center', bold: true,
    });
  });
  addFooter(s, 2, TOTAL);
}

// 3. OVERVIEW + SIGNIFICANCE
{
  const s = pptx.addSlide();
  s.addShape(pptx.shapes.RECTANGLE, { x: 0, y: 0, w: 13.333, h: 7.5, fill: { color: C.cream } });
  sectionBanner(s, '1. INTRODUCTION');
  slideTitle(s, 'Project Overview');
  bullets(s, [
    'SpotTour is a web-based tourism platform for Surigao City.',
    'It centralizes scattered information on tourist spots, hotels, and restaurants.',
    'Three interfaces: Admin, Traveller, and Owner.',
    'Helps travellers plan trips, owners promote businesses, and the tourism office oversee listings and visitor data.',
    'Supports community engagement through reviews, ratings, favorites, booking, payment, and day plans.',
  ], { y: 1.5 });
  addFooter(s, 3, TOTAL);
}

// 4. OBJECTIVES
{
  const s = pptx.addSlide();
  s.addShape(pptx.shapes.RECTANGLE, { x: 0, y: 0, w: 13.333, h: 7.5, fill: { color: C.cream } });
  sectionBanner(s, '1. INTRODUCTION');
  slideTitle(s, 'Objectives of the Study');
  s.addText('General Objective', {
    x: 0.55, y: 1.5, w: 12, h: 0.35,
    fontSize: 16, color: C.teal, fontFace: 'Calibri', bold: true,
  });
  s.addText('To design and develop SpotTour: An Interactive Platform for Tourism and Community Engagement.', {
    x: 0.55, y: 1.85, w: 12.2, h: 0.55,
    fontSize: 15, color: C.ink, fontFace: 'Calibri',
  });
  s.addText('Specific Objectives', {
    x: 0.55, y: 2.55, w: 12, h: 0.35,
    fontSize: 16, color: C.teal, fontFace: 'Calibri', bold: true,
  });
  bullets(s, [
    'Gather requirements through interviews and consultations with the local tourism office.',
    'Design and develop browsing, mapping, booking and payment, reviews, day planning, chatbot, notifications, and content management.',
    'Evaluate the system in terms of usability, functionality, and user satisfaction.',
  ], { y: 2.95, fontSize: 15, h: 3.5 });
  addFooter(s, 4, TOTAL);
}

// 5. PROBLEM
{
  const s = pptx.addSlide();
  s.addShape(pptx.shapes.RECTANGLE, { x: 0, y: 0, w: 13.333, h: 7.5, fill: { color: C.cream } });
  sectionBanner(s, '2. BACKGROUND & LITERATURE');
  slideTitle(s, 'Problem Statement');
  bullets(s, [
    'There is no one-stop interactive tourism platform in Surigao City.',
    'Information on spots, restaurants, and hotels is scattered and often outdated.',
    'Tourists and locals rely on word of mouth, brochures, social media, and manual searching.',
    'This makes travel planning difficult and can weaken the visitor experience.',
  ], { y: 1.5 });
  addFooter(s, 5, TOTAL);
}

// 6. LITERATURE SYNTHESIS
{
  const s = pptx.addSlide();
  s.addShape(pptx.shapes.RECTANGLE, { x: 0, y: 0, w: 13.333, h: 7.5, fill: { color: C.cream } });
  sectionBanner(s, '2. BACKGROUND & LITERATURE');
  slideTitle(s, 'Literature Synthesis');
  s.addShape(pptx.shapes.ROUNDED_RECTANGLE, {
    x: 0.45, y: 1.45, w: 12.4, h: 0.65,
    fill: { color: C.warnBg }, rectRadius: 0.08,
  });
  s.addText('Synthesis only — combined insights from related studies, not a full RRL listing.', {
    x: 0.65, y: 1.52, w: 12.0, h: 0.5,
    fontSize: 14, color: C.warnInk, fontFace: 'Calibri', bold: true, valign: 'middle',
  });
  const syn = [
    { h: 'What literature agrees on', b: 'Digital platforms improve tourism services, competitiveness, and visitor experience when information and transactions are in one system.' },
    { h: 'Common gap in local practice', b: 'Many local tourism businesses still use fragmented tools such as social media and basic e-payment, without integrated booking and management.' },
    { h: 'Contribution of SpotTour', b: 'SpotTour addresses this gap for Surigao City by unifying listings, community engagement, booking, and admin/owner tools in one platform.' },
  ];
  syn.forEach((item, i) => {
    const y = 2.3 + i * 1.4;
    s.addShape(pptx.shapes.ROUNDED_RECTANGLE, {
      x: 0.45, y, w: 12.4, h: 1.25,
      fill: { color: C.white }, rectRadius: 0.1,
    });
    s.addText(`${i + 1}. ${item.h}`, {
      x: 0.7, y: y + 0.15, w: 11.9, h: 0.35,
      fontSize: 15, color: C.teal, fontFace: 'Calibri', bold: true,
    });
    s.addText(item.b, {
      x: 0.7, y: y + 0.55, w: 11.9, h: 0.55,
      fontSize: 14, color: C.ink, fontFace: 'Calibri',
    });
  });
  addFooter(s, 6, TOTAL);
}

// 7. METHODOLOGY + TOOLS
{
  const s = pptx.addSlide();
  s.addShape(pptx.shapes.RECTANGLE, { x: 0, y: 0, w: 13.333, h: 7.5, fill: { color: C.cream } });
  sectionBanner(s, '3. METHODOLOGY');
  slideTitle(s, 'Methodology: RAD');
  s.addText('Rapid Application Development was used for fast prototyping, continuous feedback, and the limited capstone timeframe.', {
    x: 0.55, y: 1.4, w: 12.2, h: 0.4,
    fontSize: 14, color: C.muted, fontFace: 'Calibri',
  });
  const phases = [
    { n: '1', t: 'Requirements', d: 'Tourism office interviews; define scope' },
    { n: '2', t: 'User Design', d: 'Figma prototypes; user feedback' },
    { n: '3', t: 'Construction', d: 'Build modules, database, and UI' },
    { n: '4', t: 'Cutover', d: 'Testing and ISO/IEC 25010 evaluation' },
  ];
  phases.forEach((p, i) => {
    const x = 0.45 + i * 3.2;
    s.addShape(pptx.shapes.ROUNDED_RECTANGLE, {
      x, y: 1.95, w: 3.0, h: 2.55,
      fill: { color: C.white }, rectRadius: 0.12,
    });
    s.addText(p.n, {
      x, y: 2.15, w: 3.0, h: 0.5,
      fontSize: 22, color: C.teal, fontFace: 'Calibri', bold: true, align: 'center',
    });
    s.addText(p.t, {
      x: x + 0.15, y: 2.7, w: 2.7, h: 0.4,
      fontSize: 15, color: C.tealDark, fontFace: 'Calibri', bold: true, align: 'center',
    });
    s.addText(p.d, {
      x: x + 0.2, y: 3.2, w: 2.6, h: 0.9,
      fontSize: 13, color: C.muted, fontFace: 'Calibri', align: 'center',
    });
  });
  s.addText('Tools: PHP / Laravel  ·  MySQL  ·  HTML, CSS, JavaScript  ·  Figma  ·  PayMongo  ·  ISO/IEC 25010 + Google Forms', {
    x: 0.45, y: 4.75, w: 12.4, h: 1.4,
    fontSize: 15, color: C.ink, fontFace: 'Calibri',
  });
  addFooter(s, 7, TOTAL);
}

// 8. IMPLEMENTATION (NO DEMO)
{
  const s = pptx.addSlide();
  s.addShape(pptx.shapes.RECTANGLE, { x: 0, y: 0, w: 13.333, h: 7.5, fill: { color: C.cream } });
  sectionBanner(s, '4. IMPLEMENTATION');
  slideTitle(s, 'System Developed');
  const roles = [
    {
      t: 'Traveller',
      items: ['Browse spots, hotels, restaurants', 'Book and pay online', 'Reviews, favorites, checklists', 'Day plans, maps, chat, feedback'],
    },
    {
      t: 'Owner',
      items: ['Manage listings and packages', 'Handle booking requests', 'Track arrivals and calendar', 'View ratings, payments, chat'],
    },
    {
      t: 'Admin',
      items: ['Manage users and owners', 'Approve and oversee listings', 'Monitor bookings and feedback', 'Visitor reports and calendar'],
    },
  ];
  roles.forEach((role, i) => {
    const x = 0.4 + i * 4.25;
    s.addShape(pptx.shapes.ROUNDED_RECTANGLE, {
      x, y: 1.55, w: 4.05, h: 4.9,
      fill: { color: C.white }, rectRadius: 0.12,
      shadow: { type: 'outer', color: '000000', blur: 6, opacity: 0.08, offset: 2 },
    });
    s.addShape(pptx.shapes.RECTANGLE, {
      x, y: 1.55, w: 4.05, h: 0.75, fill: { color: C.teal },
    });
    s.addText(role.t, {
      x, y: 1.7, w: 4.05, h: 0.45,
      fontSize: 20, color: C.white, fontFace: 'Calibri', bold: true, align: 'center',
    });
    s.addText(
      role.items.map((t) => ({
        text: t,
        options: { bullet: true, breakLine: true, color: C.ink, fontSize: 14, fontFace: 'Calibri', paraSpaceAfter: 10 },
      })),
      { x: x + 0.3, y: 2.6, w: 3.45, h: 3.5 }
    );
  });
  addFooter(s, 8, TOTAL);
}

// 9. CHALLENGES
{
  const s = pptx.addSlide();
  s.addShape(pptx.shapes.RECTANGLE, { x: 0, y: 0, w: 13.333, h: 7.5, fill: { color: C.cream } });
  sectionBanner(s, '4. IMPLEMENTATION');
  slideTitle(s, 'Challenges and Solutions');
  const rows = [
    ['Unclear / scattered requirements', 'Interviews with the tourism office'],
    ['Booking and payment clarity', 'Confirmation pages, statuses, notifications'],
    ['Owner/Admin content overload', 'Improved management pages and status labels'],
    ['Accidental destructive actions', 'Confirmation dialogs for delete, approve, logout'],
    ['Need for mobile access', 'Responsive web design instead of a native app first'],
  ];
  s.addShape(pptx.shapes.RECTANGLE, {
    x: 0.45, y: 1.5, w: 12.4, h: 0.55, fill: { color: C.teal },
  });
  s.addText('Challenge', {
    x: 0.65, y: 1.58, w: 5.5, h: 0.4,
    fontSize: 14, color: C.white, fontFace: 'Calibri', bold: true,
  });
  s.addText('Solution', {
    x: 6.5, y: 1.58, w: 6, h: 0.4,
    fontSize: 14, color: C.white, fontFace: 'Calibri', bold: true,
  });
  rows.forEach((r, i) => {
    const y = 2.05 + i * 0.85;
    s.addShape(pptx.shapes.RECTANGLE, {
      x: 0.45, y, w: 12.4, h: 0.85,
      fill: { color: i % 2 === 0 ? C.white : C.softBg },
    });
    s.addText(r[0], {
      x: 0.65, y: y + 0.22, w: 5.5, h: 0.45,
      fontSize: 14, color: C.ink, fontFace: 'Calibri',
    });
    s.addText(r[1], {
      x: 6.5, y: y + 0.22, w: 6.1, h: 0.45,
      fontSize: 14, color: C.ink, fontFace: 'Calibri',
    });
  });
  addFooter(s, 9, TOTAL);
}

// 10. EVALUATION METHOD
{
  const s = pptx.addSlide();
  s.addShape(pptx.shapes.RECTANGLE, { x: 0, y: 0, w: 13.333, h: 7.5, fill: { color: C.cream } });
  sectionBanner(s, '5. RESULTS & DISCUSSION');
  slideTitle(s, 'Evaluation Method');
  bullets(s, [
    'Quality model: ISO/IEC 25010 (Functional Suitability, Usability, Reliability & Performance, Security, Usefulness & Tourism Impact).',
    'Tool: Google Forms survey using a 5-point Likert scale.',
    'Sampling: Purposive — 25 respondents (11 travellers, 6 residents, 4 owners, 2 tourism/admin, 2 student/faculty).',
    'Also used: system walkthrough, observation, and open-ended comments.',
    'Analysis: Weighted Mean  —  WM = Σ(f × w) / N',
  ], { y: 1.5, fontSize: 15 });
  addFooter(s, 10, TOTAL);
}

// 11. RESULTS
{
  const s = pptx.addSlide();
  s.addShape(pptx.shapes.RECTANGLE, { x: 0, y: 0, w: 13.333, h: 7.5, fill: { color: C.cream } });
  sectionBanner(s, '5. RESULTS & DISCUSSION');
  slideTitle(s, 'Evaluation Results');

  const stats = [
    { v: '100%', l: 'System testing pass' },
    { v: '4.33', l: 'Grand mean (Strongly Agree)' },
    { v: '92%', l: 'Satisfied / Very Satisfied' },
  ];
  stats.forEach((st, i) => {
    const x = 0.45 + i * 4.25;
    s.addShape(pptx.shapes.ROUNDED_RECTANGLE, {
      x, y: 1.5, w: 4.05, h: 1.7,
      fill: { color: C.white }, rectRadius: 0.12,
    });
    s.addText(st.v, {
      x, y: 1.65, w: 4.05, h: 0.8,
      fontSize: 32, color: C.teal, fontFace: 'Calibri', bold: true, align: 'center',
    });
    s.addText(st.l, {
      x: x + 0.2, y: 2.5, w: 3.65, h: 0.45,
      fontSize: 13, color: C.muted, fontFace: 'Calibri', align: 'center',
    });
  });

  const tableRows = [
    [
      { text: 'Criteria', options: { bold: true, color: C.white, fill: { color: C.teal } } },
      { text: 'Mean', options: { bold: true, color: C.white, fill: { color: C.teal }, align: 'center' } },
      { text: 'Interpretation', options: { bold: true, color: C.white, fill: { color: C.teal }, align: 'center' } },
    ],
    ['Functional Suitability', '4.31', 'Strongly Agree'],
    ['Usability', '4.37', 'Strongly Agree'],
    ['Reliability and Performance', '4.24', 'Strongly Agree'],
    ['Security', '4.33', 'Strongly Agree'],
    ['Usefulness and Tourism Impact', '4.42', 'Strongly Agree'],
  ];
  s.addTable(tableRows, {
    x: 0.8, y: 3.45, w: 11.7, h: 3.2,
    colW: [5.5, 2.6, 3.6],
    border: [{ type: 'solid', pt: 0.5, color: C.line }],
    fontFace: 'Calibri',
    fontSize: 13,
    color: C.ink,
    align: 'left',
    valign: 'middle',
  });
  addFooter(s, 11, TOTAL);
}

// 12. OBJECTIVES MET
{
  const s = pptx.addSlide();
  s.addShape(pptx.shapes.RECTANGLE, { x: 0, y: 0, w: 13.333, h: 7.5, fill: { color: C.cream } });
  sectionBanner(s, '5. RESULTS & DISCUSSION');
  slideTitle(s, 'Comparison with Objectives');
  bullets(s, [
    'Requirements gathered with the tourism office — Done.',
    'Admin, Traveller, and Owner features developed — Implemented.',
    'Usability, functionality, and satisfaction evaluated — Grand mean 4.33 (Strongly Agree).',
    'Highest group mean: Travellers (4.43). Owners: 4.13 (Agree).',
    'All stated objectives were met.',
  ], { y: 1.5 });
  addFooter(s, 12, TOTAL);
}

// 13. CONCLUSION
{
  const s = pptx.addSlide();
  s.addShape(pptx.shapes.RECTANGLE, { x: 0, y: 0, w: 13.333, h: 7.5, fill: { color: C.cream } });
  sectionBanner(s, '6. CONCLUSION');
  slideTitle(s, 'Conclusion');
  bullets(s, [
    'SpotTour is a working web-based tourism platform for Surigao City.',
    'It helps travellers discover and book services, owners manage listings, and administrators oversee the system.',
    'Developed using RAD with Laravel, MySQL, and a responsive web interface.',
    'Evaluation shows the system is usable, functional, reliable, secure, and useful.',
    'The project supports a more organized digital tourism experience in Surigao City.',
  ], { y: 1.5 });
  addFooter(s, 13, TOTAL);
}

// 14. RECOMMENDATIONS
{
  const s = pptx.addSlide();
  s.addShape(pptx.shapes.RECTANGLE, { x: 0, y: 0, w: 13.333, h: 7.5, fill: { color: C.cream } });
  sectionBanner(s, '6. CONCLUSION');
  slideTitle(s, 'Recommendations');
  bullets(s, [
    'Develop a native Android and iOS application.',
    'Add richer map navigation and more payment options.',
    'Improve owner/admin analytics for sales and visitor insights.',
    'Enhance chatbot responses and automatic day-plan suggestions.',
    'Explore partnership with the LGU / local tourism office.',
  ], { y: 1.5 });
  addFooter(s, 14, TOTAL);
}

// 15. LEARNING
{
  const s = pptx.addSlide();
  s.addShape(pptx.shapes.RECTANGLE, { x: 0, y: 0, w: 13.333, h: 7.5, fill: { color: C.cream } });
  sectionBanner(s, '6. CONCLUSION');
  slideTitle(s, 'Learning Experience');
  bullets(s, [
    'Translated real tourism-office needs into working system modules.',
    'Practiced iterative development through RAD: prototype, feedback, build, refine.',
    'Strengthened full-stack development, usability design, and system evaluation skills.',
    'Pair collaboration required shared planning, testing, and documentation.',
  ], { y: 1.5 });
  addFooter(s, 15, TOTAL);
}

// 16. Q&A
{
  const s = pptx.addSlide();
  s.addShape(pptx.shapes.RECTANGLE, { x: 0, y: 0, w: 13.333, h: 7.5, fill: { color: C.tealDark } });
  s.addShape(pptx.shapes.RECTANGLE, { x: 0, y: 0, w: 0.22, h: 7.5, fill: { color: C.tealMid } });
  s.addText('7. QUESTION AND ANSWER', {
    x: 0.7, y: 2.2, w: 12, h: 0.4,
    fontSize: 14, color: '99F6E4', fontFace: 'Calibri', bold: true, charSpacing: 2,
  });
  s.addText('Thank You!', {
    x: 0.7, y: 2.8, w: 12, h: 0.8,
    fontSize: 48, color: C.white, fontFace: 'Calibri', bold: true,
  });
  s.addText('Ianzel A. Morales  ·  Beatrix Coleen Beto\nQuestions are welcome.', {
    x: 0.7, y: 3.8, w: 12, h: 0.9,
    fontSize: 18, color: 'CCFBF1', fontFace: 'Calibri',
  });
  s.addText('SpotTour  ·  SNSU College of Computing & Information Sciences', {
    x: 0.7, y: 5.5, w: 12, h: 0.4,
    fontSize: 13, color: '5EEAD4', fontFace: 'Calibri',
  });
}

const out = path.join(__dirname, 'SpotTour_Final_Defense_v2.pptx');
pptx.writeFile({ fileName: out }).then(() => {
  console.log('Created:', out);
}).catch((err) => {
  console.error(err);
  process.exit(1);
});
