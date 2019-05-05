<?php
/**
 * @var \App\View\AppView $this
 * @var int $participants_offset
 * @var array $participants
 * @var string $dateType
 * @var string $autofocusIndex
 */
        for ($i = $participants_offset, $length = count($participants); $i < $length; $i++): ?>
    <tr>
        <?php
            echo '<td>' . $this->Form->control('contest.participants.' . $i . '.name', ['label' => ['text' => __('Participant name'), 'class' => 'show-for-sr'], 'autofocus' => ($i == $autofocusIndex) ]);
            echo '<td>' . $this->Form->control('contest.participants.' . $i . '.email', ['label' => ['text' => __('Participant email address'), 'class' => 'show-for-sr']]);
            echo '<td>' . $this->Form->control('contest.participants.' . $i . '.date_of_birth', ['type' => $dateType,  'label' => ['text' => __('Participant date of birth'), 'class' => 'show-for-sr']]);
            echo '<td class="trashbin">';
            echo $this->Form->control('contest.participants.' . $i . '.mark_for_deletion', ['type' => 'checkbox', 'title' => __('Delete participant'), 'label' => ['text' => __('Mark participant for deletion'), 'class' => 'show-for-sr']]);
            echo $this->Form->control('contest.participants.' . $i . '.dynnew', ['type' => 'hidden']);
        ?>
    </tr>
    <?php endfor; ?>
