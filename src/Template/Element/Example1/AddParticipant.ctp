<?php
/**
 * @var \App\Model\Entity\Contest $contest
 * @var string $dateType
 */

 $participants = $contest['participants'];
 echo $this->element('Example1/ContestParticipants', [
   "participants" => $participants,
   "participants_offset" => (count($participants) - 1),
 ]);
?>
