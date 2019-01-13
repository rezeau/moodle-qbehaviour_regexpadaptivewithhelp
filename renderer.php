<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Renderer for outputting parts of a question belonging to the
 * regexp (with help) behaviour.
 *
 * @package    qbehaviour
 * @subpackage regexp
 * @copyright  2011 Tim Hunt & Joseph R�zeau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../adaptive/renderer.php');
/**
 * Renderer for outputting parts of a question belonging to the legacy
 * adaptive behaviour.
 *
 * @copyright  2011 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class qbehaviour_regexpadaptivewithhelp_renderer extends qbehaviour_adaptive_renderer {

    protected function get_graded_step(question_attempt $qa) {
        foreach ($qa->get_reverse_step_iterator() as $step) {
            if ($step->has_behaviour_var('_try')) {
                return $step;
            }
        }
    }

    // Moved help strings to this function to get appropriate strings for the nopenalty behaviour.
    public function help_msg() {
        $helptexts = array();
        $helptexts[1] = get_string('buyletter', 'qbehaviour_regexpadaptivewithhelp');
        $helptexts[2] = get_string('buyword', 'qbehaviour_regexpadaptivewithhelp');
        $helptexts[3] = get_string('buywordorpunctuation', 'qbehaviour_regexpadaptivewithhelp');
        return $helptexts;
    }

    // Display the "Help" button.
    public function controls(question_attempt $qa, question_display_options $options, $helptext='') {
        // If student's answer is no longer improvable, then there's no point enabling the hint button.
        $isimprovable = $qa->get_behaviour()->is_state_improvable($qa->get_state());
        $output = $this->submit_button($qa, $options).'&nbsp;';
        $helpmode = $qa->get_question()->usehint;

        if ($helpmode == 0 || $options->readonly || !$isimprovable) {
            return $output;
        }
        $helptext = $this->help_msg()[$helpmode];
        $attributes = array(
            'type' => 'submit',
            'id' => $qa->get_behaviour_field_name('helpme'),
            'name' => $qa->get_behaviour_field_name('helpme'),
            'value' => $helptext,
            'class' => 'submit btn',
        );

        $attributes['round'] = true;
        $output .= html_writer::empty_tag('input', $attributes);
            $this->page->requires->js_init_call('M.core_question_engine.init_submit_button',
                    array($attributes['id'], $qa->get_slot()));
        return $output;
    }

    /**
     * Display the extra help for the student, if it was requested.
     * @param question_attempt $qa a question attempt.
     * @param question_display_options $options controls what should and should not be displayed.
     */

    public function extra_help(question_attempt $qa, question_display_options $options) {
        return html_writer::nonempty_tag('div', $qa->get_behaviour()->get_extra_help_if_requested($options->markdp));
    }

    public function feedback(question_attempt $qa, question_display_options $options) {
        // Try to find the last graded step.
        $gradedstep = $this->get_graded_step($qa);
        if ($gradedstep) {
            if ($gradedstep->has_behaviour_var('_helps') ) {
                return $this->extra_help($qa, $options);
            }
        }
        if (is_null($gradedstep) || $qa->get_max_mark() == 0 ||
                $options->marks < question_display_options::MARK_AND_MAX) {
            return '';
        }

        // Let student know wether the answer was correct.
        if ($qa->get_state()->is_commented()) {
            $class = $qa->get_state()->get_feedback_class();
        } else {
            $class = question_state::graded_state_for_fraction(
                    $gradedstep->get_behaviour_var('_rawfraction'))->get_feedback_class();
        }
        $penalty = $qa->get_question()->penalty;
        if ($penalty != 0) {
            $gradingdetails = $this->render_adaptive_marks(
                $qa->get_behaviour()->get_adaptive_marks(), $options);
        }
        $output = '';
        $output .= html_writer::tag('div', $gradingdetails,
                array('class' => 'gradingdetails'));

        return $output;
    }
}
