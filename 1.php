<?php 

function read_file_obj($file_name){
	$str = '';
	$str_res = array();
	
	//	id,subject_id,title,chapter_id,topic,total_questions
	$myfile = fopen($file_name, "r") or die("Unable to open file!");
	while(!feof($myfile)){ 
		$str = fgets($myfile);	
		$obj = new stdClass();
		$str_arr = explode(',', $str);
		//	$obj->id = $str_arr[0];
		$obj->subject_id = $str_arr[1];
		//	$obj->title = $str_arr[2];
		$obj->chapter_id = $str_arr[3];
		//	$obj->topic = $str_arr[4];
		//	$obj->total_questions = $str_arr[5];
		array_push($str_res, $obj);
		//	$str_res.push(;
	}
	fclose($myfile);
	
	return $str_res;
}
function striptagsfromdata($data){
	return strip_tags($data);
}
function write_string_to_file($myFile = '', $stringData = ''){
	
	//	$myFile = "testFile.txt";
	$fh = fopen($myFile, 'a') or die("can't open file");
	//	$stringData = "New Stuff 1\n";
	//	fwrite($fh, $stringData);
	//	$stringData = "New Stuff 2\n";
	fwrite($fh, $stringData);
	fclose($fh);
}

include_once("common/db.php");

$final_str = read_file_obj('CPL_Questions_List_Edited.csv');

$ff_arr_data = array();

// echo '<br /><br /><br /><br /><pre>';

foreach($final_str as $vrec){
	$res2 = "SELECT id, question FROM ot_cpl_questions where subject_id = ".$vrec->subject_id." and chapter_id = " . $vrec->chapter_id ;	
	$result = executeQuery($res2);
	$chap_names  = "select id,title from ot_chapters where id=".$vrec->chapter_id ;
	$chapter_result = executeQuery($chap_names);
	$subj_names  = "select id,title from ot_subjects where id=".$vrec->subject_id ;
	$subject_result = executeQuery($subj_names);
	$filename = "CPL_". $subject_result[0]['title']."_". $chapter_result[0]['title'].".html";
	foreach($result as $rressss){
		$ress = new stdClass();
		$ress->id = $rressss['id'];
		$ress->question = $rressss['question'];
		$ress->subject_id = $vrec->subject_id;
		$ress->chapter_id = $vrec->chapter_id;
		array_push($ff_arr_data, $ress);
	}
	
	$count = 1;
	foreach($ff_arr_data as $ffarr){	
		//	echo '<br /><br />';
		$write_str = $count++ . ')&nbsp; ' . striptagsfromdata($ffarr->question) . '<br />';
		$query = "SELECT id, `option`, is_correct FROM ot_cpl_question_options WHERE question_id = ". $ffarr->id;
		$result = executeQuery($query);
		$ccount = 'A';
		foreach($result as $rressss){
			$write_str .= $ccount++ . ')&nbsp;' . (intval($rressss['is_correct']) == 1 ? '<span style="color:green">' : '<span>');
			$write_str .= ' ' . striptagsfromdata($rressss['option']) . '</span><br />';
		}
		write_string_to_file($filename, $write_str);
	}
	$ff_arr_data = array();
//	break;
}

echo '*************************** Completed writing to the file ***************************';

/*
	[id] => 22738
    [question] => Regulations which refer to "commercial operators" relate to that person who
    [subject_id] => 71
    [chapter_id] => 419
*/



//	print_r($ff_arr_data);


/*

$res2 = "SELECT * FROM ot_cpl_questions ORDER BY id ASC LIMIT 10 ";

$res2 = executeQuery($res2);

$id = '';
foreach($res2 as $re){
	$id .= $re['id'].',';
}
$id = substr(trim($id), 0, -1);

*/

//	include_once("common/footer.php");