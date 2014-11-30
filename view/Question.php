<?php

namespace view;

class Question {

	// Visar en fråga samt svarsalternativ och eventuell bild
    public function getHTML(\model\Question $question) {

        $html = '<h2>' . $question->getQuestion() . '</h2>';

        if ($question->getMediaPath() !== NULL) {
            $html .= '<img src="' . $question->getMediaPath() . '" />';
        }
       
        $answers = $question->getAnswers();
        shuffle($answers);

        $html .= '<form method="POST">
					<div id="answersForm">';
	
        foreach ($answers as $answer) {
            $html .= '<label class="answers">' .
                        '<input type="radio" name="' . \view\QuizzyMaster::$ANSWER . '" value="' . $answer . '" required />'
                     . $answer . '</label>';
        }

        $html .= '<p><input type="submit" value="Svara"/></p>
				  <input type="hidden" value="' . $question->getQuestionId() . '" name="' . \view\QuizzyMaster::$QUESTION_ID . '" />
				  </div>
				  </form>';
				  
        return $html;
    }
}
