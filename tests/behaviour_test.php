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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace qbehaviour_regexpadaptivewithhelp;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/question/behaviour/regexpadaptivewithhelp/behaviour.php');

/**
 * Tests for the RegExp adaptive behaviour with help.
 *
 * @package    qbehaviour_regexpadaptivewithhelp
 * @copyright  2026 RegExp plugin maintainers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class behaviour_test extends \advanced_testcase {
    /**
     * The penalty helper must emit balanced markup when a total is flagged.
     */
    public function test_flagged_penalty_has_balanced_markup(): void {
        $reflection = new \ReflectionClass(\qbehaviour_regexpadaptivewithhelp::class);
        $behaviour = $reflection->newInstanceWithoutConstructor();

        $output = $behaviour->get_help_penalty(1, 2, 'totalpenalties');

        $this->assertStringContainsString('<span class="flagged-tag">1.00</span>', $output);
        $this->assertSame(substr_count($output, '<span'), substr_count($output, '</span>'));
    }
}
