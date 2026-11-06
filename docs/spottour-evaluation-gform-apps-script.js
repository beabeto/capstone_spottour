/**
 * SpotTour System Evaluation Survey — Google Forms generator (Apps Script)
 *
 * Paste this entire file into https://script.google.com (New project),
 * then run: createSpotTourEvaluationForm
 *
 * After Run finishes: View → Logs to copy the live form links.
 */

function createSpotTourEvaluationForm() {
  var form = FormApp.create(
    'SpotTour: An Interactive Platform for Tourism and Community Engagement — System Evaluation Survey'
  );

  form.setDescription(
    'You are invited to evaluate SpotTour, a web-based tourism platform for Surigao City. ' +
    'Your responses will help assess the system\'s usefulness, usability, and overall quality. ' +
    'This survey is for academic research / capstone evaluation purposes only. ' +
    'Please answer honestly based on your experience using the system.\n\n' +
    'Rating scale for Sections 2–6:\n' +
    '1 = Strongly Disagree | 2 = Disagree | 3 = Neutral | 4 = Agree | 5 = Strongly Agree'
  );

  form.setCollectEmail(false);
  form.setAllowResponseEdits(false);
  form.setLimitOneResponsePerUser(false);
  form.setProgressBar(true);
  form.setConfirmationMessage(
    'Thank you for evaluating SpotTour. Your feedback is greatly appreciated.'
  );

  // ===== Section 1: Respondent Profile (first page) =====
  form.addSectionHeaderItem()
    .setTitle('Section 1: Respondent Profile')
    .setHelpText('Please tell us a little about yourself.');

  form.addTextItem()
    .setTitle('Full Name (Optional)')
    .setRequired(false);

  form.addMultipleChoiceItem()
    .setTitle('Age')
    .setChoiceValues(['18–24', '25–34', '35–44', '45 and above'])
    .setRequired(true);

  form.addMultipleChoiceItem()
    .setTitle('Gender')
    .setChoiceValues(['Male', 'Female', 'Prefer not to say'])
    .setRequired(true);

  form.addMultipleChoiceItem()
    .setTitle('Role / Type of Respondent')
    .setChoiceValues([
      'Traveller / Tourist',
      'Local Resident',
      'Business Owner',
      'Tourism Office / Administrator',
      'Student / Faculty Evaluator'
    ])
    .setRequired(true);

  form.addMultipleChoiceItem()
    .setTitle('How often do you use tourism-related websites or apps?')
    .setChoiceValues(['Rarely', 'Occasionally', 'Often', 'Very often'])
    .setRequired(true);

  // ===== Section 2: Functional Suitability =====
  form.addPageBreakItem()
    .setTitle('Section 2: Functional Suitability')
    .setHelpText(
      'Rate each statement from 1 to 5.\n' +
      '1 = Strongly Disagree · 2 = Disagree · 3 = Neutral · 4 = Agree · 5 = Strongly Agree'
    );

  addLikertItems_(form, [
    'SpotTour provides complete and useful tourism information (spots, hotels, and restaurants).',
    'The booking features (spot, hotel, and food reservations) work according to my needs.',
    'Online payment options are clear and useful.',
    'Features such as reviews, ratings, favorites, bookmarks, and checklists are helpful.',
    'Map location and day-plan features support tourism planning effectively.',
    'The system supports the needs of its intended users (travellers, owners, and/or administrators).'
  ]);

  // ===== Section 3: Usability =====
  form.addPageBreakItem()
    .setTitle('Section 3: Usability / Ease of Use')
    .setHelpText(
      'Rate each statement from 1 to 5.\n' +
      '1 = Strongly Disagree · 2 = Disagree · 3 = Neutral · 4 = Agree · 5 = Strongly Agree'
    );

  addLikertItems_(form, [
    'The system interface is easy to understand and navigate.',
    'I can easily find tourist spots, hotels, and restaurants in SpotTour.',
    'The booking and payment process is clear and easy to follow.',
    'Buttons, menus, and labels are easy to understand.',
    'I can learn how to use SpotTour with little or no assistance.',
    'The overall design of SpotTour is user-friendly and visually clear.'
  ]);

  // ===== Section 4: Reliability / Performance =====
  form.addPageBreakItem()
    .setTitle('Section 4: Reliability and Performance')
    .setHelpText(
      'Rate each statement from 1 to 5.\n' +
      '1 = Strongly Disagree · 2 = Disagree · 3 = Neutral · 4 = Agree · 5 = Strongly Agree'
    );

  addLikertItems_(form, [
    'The system works properly during browsing, booking, and account-related tasks.',
    'Pages and features load within an acceptable time.',
    'The system responds correctly when I submit forms or complete transactions.',
    'SpotTour performs consistently without frequent errors or interruptions.',
    'The system is usable on both desktop and mobile devices.'
  ]);

  // ===== Section 5: Security =====
  form.addPageBreakItem()
    .setTitle('Section 5: Security and Data Protection')
    .setHelpText(
      'Rate each statement from 1 to 5.\n' +
      '1 = Strongly Disagree · 2 = Disagree · 3 = Neutral · 4 = Agree · 5 = Strongly Agree'
    );

  addLikertItems_(form, [
    'Login and account features appear secure and appropriate.',
    'I feel that personal account information is handled carefully.',
    'Payment-related pages look trustworthy and properly protected.',
    'The system provides adequate confirmation messages for important actions (booking, payment, updates).'
  ]);

  // ===== Section 6: Usefulness / Tourism Impact =====
  form.addPageBreakItem()
    .setTitle('Section 6: Usefulness and Tourism Impact')
    .setHelpText(
      'Rate each statement from 1 to 5.\n' +
      '1 = Strongly Disagree · 2 = Disagree · 3 = Neutral · 4 = Agree · 5 = Strongly Agree'
    );

  addLikertItems_(form, [
    'SpotTour helps improve access to tourism information in Surigao City.',
    'The system can help promote local tourist spots, hotels, and restaurants.',
    'SpotTour can support community engagement through reviews, ratings, and shared feedback.',
    'I would recommend SpotTour to tourists, residents, or local business owners.',
    'Overall, SpotTour is useful as a digital tourism platform.'
  ]);

  // ===== Section 7: Overall Satisfaction =====
  form.addPageBreakItem()
    .setTitle('Section 7: Overall Satisfaction')
    .setHelpText('Please share your overall rating and comments.');

  form.addMultipleChoiceItem()
    .setTitle('Overall, how satisfied are you with SpotTour?')
    .setChoiceValues([
      'Very Dissatisfied',
      'Dissatisfied',
      'Neutral',
      'Satisfied',
      'Very Satisfied'
    ])
    .setRequired(true);

  form.addParagraphTextItem()
    .setTitle('What did you like most about SpotTour?')
    .setRequired(true);

  form.addParagraphTextItem()
    .setTitle('What features should be improved?')
    .setRequired(true);

  form.addParagraphTextItem()
    .setTitle('Additional comments or suggestions (Optional)')
    .setRequired(false);

  var editUrl = form.getEditUrl();
  var publishedUrl = form.getPublishedUrl();

  Logger.log('SpotTour evaluation form created successfully.');
  Logger.log('Edit URL (admin / edit form): ' + editUrl);
  Logger.log('Published response URL (share with respondents): ' + publishedUrl);

  return {
    editUrl: editUrl,
    publishedUrl: publishedUrl
  };
}

/** Helper: add Likert linear scale items (1–5). */
function addLikertItems_(form, questions) {
  for (var i = 0; i < questions.length; i++) {
    form.addScaleItem()
      .setTitle(questions[i])
      .setBounds(1, 5)
      .setLabels('Strongly Disagree', 'Strongly Agree')
      .setRequired(true);
  }
}
