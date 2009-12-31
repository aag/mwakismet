<?php
/* 
 * Unit tests for the Akismet Mediawiki extension.
 *
 * Started on: 2007.10.16
 *
 * Copyright 2007 Adam Goforth
 *
 * This file is part of Mediawiki-Akismet.
 *
 * Mediawiki-Akismet is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * Mediawiki-Akismet is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Mediawiki-Akismet.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!defined('SIMPLE_TEST')){
    define('SIMPLE_TEST', '/usr/local/apache2/htdocs/SimpleTest/');
}
require_once(SIMPLE_TEST . 'unit_tester.php');
require_once(SIMPLE_TEST . 'reporter.php');

require_once('./includes/MwAkismet.class.php');

// Set up the global variables for the MWAkismet class
$wgMWAkismetKey = '69a535815104';
$wgMWAkismetURL = 'http://plugintesting.example.com/';

class TestMwAkismet extends UnitTestCase {
    function TestMwAkismet() {
        $this->UnitTestCase();
    }
    
    function testExtractDiffEqual(){
        $mwAkismet = new MwAkismet();
        
        // Two empty strings
        $oldText = "";
        $newText = "";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "");
        
        // Two short (single line) strings
        $oldText = "one";
        $newText = "one";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "");
        
        // Two longer (multi-line) strings
        $oldText = "one\ntwo\nthree";
        $newText = "one\ntwo\nthree";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "");
    }
    
    function testExtractDiffRemoveLines(){
        $mwAkismet = new MwAkismet();
        
        // Remove 1 line from a 1-line text
        $oldText = "one";
        $newText = "";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "");
        
        // Remove 1 line from a 2-line text
        $oldText = "one\ntwo";
        $newText = "one";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "");
        
        $oldText = "one\ntwo";
        $newText = "two";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "");
        
        // Remove 1 line from a 3-line text
        $oldText = "one\ntwo\nthree";
        $newText = "one\nthree";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "");
        
        // Remove 2 lines from a 3-line text
        $oldText = "one\ntwo\nthree";
        $newText = "one";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "");
        
        $oldText = "one\ntwo\nthree";
        $newText = "two";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "");
        
        $oldText = "one\ntwo\nthree";
        $newText = "three";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "");
        
        // Remove 2 lines from a 4-line text
        $oldText = "one\ntwo\nthree\nfour";
        $newText = "one\nthree";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "");
        
        $oldText = "one\ntwo\nthree\nfour";
        $newText = "two\nfour";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "");
        
        $oldText = "one\ntwo\nthree\nfour";
        $newText = "one\nfour";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "");
        
        $oldText = "one\ntwo\nthree\nfour";
        $newText = "two\nthree";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "");
        
        // Remove 2 lines from a 5-line text
        $oldText = "one\ntwo\nthree\nfour\nfive";
        $newText = "one\nthree\nfive";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "");
    }
    
    function testExtractDiffNewBeginning(){
        $mwAkismet = new MwAkismet();
        
        // Add 1 line to a 1-line text
        $oldText = "one";
        $newText = "zero\none";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "zero\n");
    }
    
    function testExtractDiffNewEnd(){
        $mwAkismet = new MwAkismet();
        
        // Add 1 line to a 1-line text
        $oldText = "one";
        $newText = "one\ntwo";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "two\n");
    }
    
    function testExtractDiffNewMiddle(){
        $mwAkismet = new MwAkismet();
        
        // Add 1 line to a 2-line text
        $oldText = "one\ntwo";
        $newText = "one\nnew1\ntwo";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "new1\n");
        
        // Add 2 lines to a 2-line text
        $oldText = "one\ntwo";
        $newText = "one\nnew1\nnew2\ntwo";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "new1\nnew2\n");
        
        // Add 2 lines to a 3-line text
        $oldText = "one\ntwo\nthree";
        $newText = "one\nnew1\ntwo\nnew2\nthree";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "new1\nnew2\n");
    }
    
    function testExtractDiffAddAndRemove(){
        $mwAkismet = new MwAkismet();
        
        // Add 1 line and remove 1 line from a 1-line text
        $oldText = "one";
        $newText = "two";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "two\n");
        
        // Add 1 line and remove 1 line from a 2-line text
        $oldText = "one\ntwo";
        $newText = "new1\ntwo";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "new1\n");
        
        $oldText = "one\ntwo";
        $newText = "one\nnew2";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "new2\n");
        
        // Add 1 line and remove 1 line from a 3-line text
        $oldText = "one\ntwo\nthree";
        $newText = "one\nnew2\nthree";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "new2\n");
        
        // Add 1 line and remove 2 lines from a 3-line text
        $oldText = "one\ntwo\nthree";
        $newText = "one\nnew2";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "new2\n");
        
        // Add 2 lines and remove 1 line from a 3-line text
        $oldText = "one\ntwo\nthree";
        $newText = "one\nnew2\nnew3\nthree";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "new2\nnew3\n");
        
        // Add 2 lines and remove 2 lines from a 3-line text
        $oldText = "one\ntwo\nthree";
        $newText = "one\nnew2\nnew3";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "new2\nnew3\n");
        
        // Replace all 3 lines in a 3-line text
        $oldText = "one\ntwo\nthree";
        $newText = "new1\nnew2\nnew3";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "new1\nnew2\nnew3\n");
    }
    
    function testExtractDiffChangedLine(){
        $mwAkismet = new MwAkismet();
        
        // Change first line
        $oldText = "one\ntwo\nthree";
        $newText = "once\ntwo\nthree";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "once\n");
        
        // Change middle line
        $oldText = "one\ntwo\nthree";
        $newText = "one\ntween\nthree";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "tween\n");
        
        // Change last line
        $oldText = "one\ntwo\nthree";
        $newText = "one\ntwo\nthrice";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "thrice\n");
        
        // Change two lines in the middle
        $oldText = "one\ntwo\nthree\nfour";
        $newText = "one\ntwice\nthrice\nfour";
        
        $result = $mwAkismet->extractDiff($oldText, $newText);
        $this->assertEqual($result, "twice\nthrice\n");
    }
    
    function testQueryAkismet(){
        $spamText = '[http://samsung-6320f.unfeeble.info/ samsung 6320f] [http://indulger.info/index2_250.html home] [http://www-3pic-com.burblers.info/ www 3pic com] [http://pony-express.batswing.info/ pony express] [http://downslip.info/index6_666.html url] [http://lword.misserve.info/ lword] [http://rover-25-club.downslip.info/ rover 25 club] [http://nic.burblers.info/ nic] [http://how-bot.misserve.info/ how bot] [http://www-tucarro-con.burblers.info/ www tucarro con] [http://misserve.info/index8_250.html homepage] [http://nilox-160.misserve.info/ nilox 160] [http://meskina.stroam.info/ meskina] [http://www-gucci.downslip.info/ www gucci] [http://wop-1651-ii.wrawler.info/ wop 1651 ii] [http://wrawler.info/index3_666.html homepage] [http://stroam.info/index7_666.html home] [http://batswing.info/index4_250.html site] [http://master-prisma.batswing.info/ master prisma] [http://mos.downslip.info/ mos] [http://lauri-volpi.downslip.info/ lauri volpi] [http://clipping.wrawler.info/ clipping] [http://the-flyer.downslip.info/ the flyer] [http://your-love-is.downslip.info/ your love is] [http://driver-lexmark-z34.indulger.info/ driver lexmark z34] [http://bowl.indulger.info/ bowl] [http://sms-tenero.wrawler.info/ sms tenero] [http://nalut.misserve.info/ nalut] [http://armada.burblers.info/ armada] [http://downslip.info/index1_1000.html url] [http://saeco-roma-magic.wrawler.info/ saeco roma magic] [http://navi-crociera-msc.misserve.info/ Navi crociera msc] [http://hercules-muse-pocket-usb.indulger.info/ hercules muse pocket usb] [http://teka.wrawler.info/ teka] [http://burblers.info/index2_250.html url] [http://dragon-ballz.wrawler.info/ dragon ballz] [http://criminal-tango.misserve.info/ criminal tango] [http://ulva.indulger.info/ ulva] [http://english-channel.wrawler.info/ english channel] [http://gounod-s-faust.misserve.info/ gounod s faust] [http://hercules-wireless.batswing.info/ hercules wireless] [http://find-miner.wrawler.info/ find miner] [http://nec-lcd92vm.unfeeble.info/ nec lcd92vm] [http://mirs.misserve.info/ mirs] [http://indulger.info/index5_250.html site] [http://powermust-800.batswing.info/ powermust 800] [http://misserve.info/index7_666.html homepage] [http://nagly.indulger.info/ nagly] [http://unfeeble.info/index4_250.html map] [http://pc-card-gprs.stroam.info/ pc card gprs] [http://netgear-router-wgt634u.downslip.info/ netgear router wgt634u] [http://misserve.info/index1_666.html site] [http://ragge.burblers.info/ ragge] [http://powershot.misserve.info/ powershot] [http://tv-lcd-mivar.unfeeble.info/ tv lcd mivar] [http://doncha.misserve.info/ doncha] [http://intruders.indulger.info/ intruders] [http://vanny.indulger.info/ vanny] [http://wrawler.info/index2_250.html link] [http://granny-hairy.downslip.info/ Granny hairy] [http://tc-e17-ed.burblers.info/ tc e17 ed] [http://lexmark-z45.wrawler.info/ lexmark z45] [http://www-jumpy.indulger.info/ www jumpy] [http://hahn-air.indulger.info/ hahn air] [http://lesbo-hard.wrawler.info/ Lesbo hard] [http://www-imetec.unfeeble.info/ www imetec] [http://et4.unfeeble.info/ et4] [http://ram-ddr-256mb.batswing.info/ ram ddr 256mb] [http://indulger.info/index2_250.html home] [http://bagdad-iraq.unfeeble.info/ bagdad iraq] [http://koinadugu.stroam.info/ koinadugu] [http://fasch.wrawler.info/ fasch] [http://juven.downslip.info/ juven] [http://indulger.info/index1_666.html homepage] [http://blog-kikka.burblers.info/ Blog kikka] [http://downslip.info/index4_1000.html home] [http://susan-hayward.downslip.info/ susan hayward] [http://indulger.info/ indulger.info] [http://micro-hi-fi-teac.misserve.info/ micro hi fi teac] [http://indulger.info/index1_666.html homepage] [http://tower-thermaltake.indulger.info/ tower thermaltake] [http://batswing.info/index5_666.html homepage] [http://fermi-enrico.unfeeble.info/ fermi enrico] [http://maxtor-300gb-maxline-iii.misserve.info/ maxtor 300gb maxline iii] [http://vidoe.unfeeble.info/ vidoe] [http://francesko.indulger.info/ francesko] [http://goal-champion-s-league.unfeeble.info/ goal champion s league] [http://emploiquebec-net.burblers.info/ emploiquebec net] [http://hp-430-a1.burblers.info/ hp 430 a1] [http://i-don-t-wanna-miss-a-thing.unfeeble.info/ i don t wanna miss a thing] [http://east-hartford.downslip.info/ east hartford] [http://asus-p4pe2.downslip.info/ asus p4pe2] [http://ique-m5.stroam.info/ ique m5] [http://seeker.wrawler.info/ seeker] [http://jjlo.misserve.info/ jjlo] [http://frankee-right-back.burblers.info/ frankee right back] [http://bmw-330ci.wrawler.info/ bmw 330ci] [http://asus-6600gt.downslip.info/ asus 6600gt] [http://microsoft-small-server-2003.batswing.info/ microsoft small server 2003] [http://augusten-burroughs.stroam.info/ augusten burroughs]';
        
        $mwAkismet = new MwAkismet();
        
        // This is guaranteed to return true, because of the author field
        // http://akismet.com/development/api/
        $isSpam = $mwAkismet->queryAkismet("viagra-test-123", "spamtext", "");
        $this->assertEqual($isSpam, true);
        
        // This text is super spammy, so hopefully Akismet will recognize it as spam.
        $isSpam = $mwAkismet->queryAkismet("linktome", $spamText, "");
        $this->assertEqual($isSpam, true);
        
        // This should look like a valid comment.
        $isSpam = $mwAkismet->queryAkismet("joe", "It is true.  I could not agree more with what you are saying.  Thanks for the post.", "");
        $this->assertEqual($isSpam, false);
    }
}

$test = &new TestMwAkismet();
$test->run(new HtmlReporter());

?>
