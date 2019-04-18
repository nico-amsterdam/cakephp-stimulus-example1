<?php
/**
 * @var \App\Form\Example1Form $example1
 * @var string $dateType
 */

 $participants = $example1->getData('contest.participants');
 echo $this->element('Example1/ContestParticipants', [
   "participants" => $participants,
   "participants_offset" => (count($participants) - 1),
 ]);
?>
