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
 * @package    block_bookmarks
 * @copyright  Copyright 2013 onwards University of Split, Faculty of Economics 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// MUST HAVE
$string['pluginname'] = 'Bookmarks (korisničke oznake Book poglavlja)';
$string['bookmarks'] = 'Bookmarks'; // block title
$string['bookmarks:addinstance'] = 'Kreiraj novi Bookmarks block';
$string['bookmarks:myaddinstance'] = 'Kreiraj novi Bookmarks block na mojoj naslovnoj Moodle stranici';

// LISTING BOOKMARK ITEMS
$string['untitled-bkm-item'] = 'Oznaka bez naslova';
$string['user-bookmarks-title'] = 'Korisničke oznake';
$string['no-bookmarks'] = 'Ne postoje korisničke oznake u ovom poglavlju';
$string['bookmarks-editor'] = 'Ažuriranje svih korisničkih oznaka';
$string['aria-start-pin'] = 'Početak oznake'; // beginning of the user bookmark xx
$string['aria-end-pin'] = 'Kraj oznake. Pritisnite ovdje za povratak na popis oznaka ili nastavite čitati dalje'; // access to jump back to the list

// CREATING BOOKMARK ITEMS
$string['bookmarks-creation-title'] = 'Unos nove oznake';
$string['enter-title'] = 'Unesite naziv i pritisnite enter';
$string['btn-add-bookmark'] = 'Dodaj';
$string['creation-success'] = 'Korisnička oznaka je kreirana. Pritisnite ovdje za povratak na tekst poglavlja';
$string['creation-instructions'] = 'Da biste kreirali korisničku oznaku prvo označite željeni tekst poglavlja, zatim pritisnite <strong>Ctrl+Shift+Space</strong>, unesite naziv oznake i pritisnite <strong>Enter</strong>';

// NOTES AND WARNINGS
$string['sr-note'] = 'Upozorenje:';
$string['test-phase-note'] = 'Ovo je testna verzija blocka. Neke funkcionalnosti još nisu implementirane. Važno je naglasiti da ukoliko se sadržaj poglavlja izmijeni, postoji mogućnost da se izgubi konzistentnost oznaka. Dodatak radi u svim novijim web pretraživačima i u Internet Exploreru verzije 9 i novije';
$string['browser-unsupported'] = 'Ovaj web preglednik nije podržan. Molimo Vas da koristite neki morderni web preglednik. Dodatak nije u mogućnosti funkcionirati ispravno';
$string['no-js'] = 'Javascript u vašem web pregledniku nije omogućen. Molimo Vas da omogućite Javascript ili koristite drugi web preglednik. Dodatak nije u mogućnosti funkcionirati ispravno';

// CONFIGURATION FORM
$string['config_test_warning_enabled'] = 'Testna verzija';
$string['config_test_warning_enabled_checkbox'] = 'Prikaži upozorenje da se radi o testnoj beta verziji dodatka';
$string['config_test_warning_enabled_help'] = $string['test-phase-note'];

// EDITING BOOKMARKS
$string['editing_page_title'] = $string['bookmarks-editor'];