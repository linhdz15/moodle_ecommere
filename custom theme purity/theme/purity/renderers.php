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

require_once('renderers/blog_renderer.php');

/**
 * Class theme_h5pmod_mod_hvp_renderer
 *
 * Extends the H5P renderer so that we are able to override the relevant
 * functions declared there
 */
class theme_purity_mod_hvp_renderer extends plugin_renderer_base {

    public function hvp_alter_semantics(&$semantics, $name, $majorVersion, $minorVersion) {
    }

    public function hvp_alter_styles(&$styles, $libraries, $embedType) {
    }

    public function hvp_alter_filtered_parameters(&$parameters, $name, $majorVersion, $minorVersion) {
    }

    public function hvp_alter_scripts(&$scripts, $libraries, $embedType) {
        global $CFG;
        if (
            isset($libraries['H5P.CoursePresentation']) &&
            $libraries['H5P.CoursePresentation']['majorVersion'] == '1'
        ) {
            $include_file = ($embedType === 'editor' ? 'customPresentationEditor.js' : 'customPresentation.js');
            $scripts[] = (object) array(
                'path'    => $CFG->httpswwwroot . '/theme/purity/js/' . $include_file,
                'version' => '?ver=0.0.1',
            );
        } else if (
            isset($libraries['H5P.DragQuestion']) &&
            $libraries['H5P.DragQuestion']['majorVersion'] == '1'
        ) {
            $include_file = ($embedType === 'editor' ? 'customDragDropEditor.js' : 'customDragDrop.js');
            $scripts[] = (object) array(
                'path'    => $CFG->httpswwwroot . '/theme/purity/js/' . $include_file,
                'version' => '?ver=0.0.1',
            );
        } else if (
            isset($libraries['H5P.Flashcards']) &&
            $libraries['H5P.Flashcards']['majorVersion'] == '1'
        ) {
            $include_file = ($embedType === 'editor' ? 'customFlashcardEditor.js' : 'customFlashcard.js');
            $scripts[] = (object) array(
                'path'    => $CFG->httpswwwroot . '/theme/purity/js/' . $include_file,
                'version' => '?ver=0.0.1',
            );
        } else if (
            isset($libraries['H5P.QuestionSet']) &&
            $libraries['H5P.QuestionSet']['majorVersion'] == '1'
        ) {
            $include_file = ($embedType === 'editor' ? 'customQuestionSetEditor.js' : 'customQuestionSet.js');
            $scripts[] = (object) array(
                'path'    => $CFG->httpswwwroot . '/theme/purity/js/' . $include_file,
                'version' => '?ver=0.0.1',
            );
        }
    }
}