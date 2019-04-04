<?php
/**
 * @var \App\View\AppView $this
 * @var int $participants_offset
 * @var array $participants
 */
        $isMSIE = strpos($this->request->getHeaderLine('User-Agent'), 'Trident/') !== false;
        for ($i = $participants_offset, $length = $participants_offset + count($participants); $i < $length; $i++): ?>
    <tr>
        <?php
            echo '<td class="trashbin">';
            echo $this->Form->control('contest.participants.' . $i . '.mark_for_deletion', ['type' => 'checkbox', 'label' => false]);
            echo $this->Form->control('contest.participants.' . $i . '.dynnew', ['type' => 'hidden']);
            echo '<td>' . $this->Form->control('contest.participants.' . $i . '.name', ['label' => false, 'required' => false]);
            echo '<td>' . $this->Form->control('contest.participants.' . $i . '.email', ['label' => false, 'required' => true]);
            echo '<td>' . $this->Form->control('contest.participants.' . $i . '.date_of_birth', ['type' => ($isMSIE ? 'date' : 'datepicker'),  'label' => false, 'required' => true]);
        ?>
    </tr>
    <?php endfor; ?>
