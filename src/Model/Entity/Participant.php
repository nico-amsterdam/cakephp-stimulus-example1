<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Participant Entity
 *
 * @property int $id
 * @property int $contest_id
 * @property string $name
 * @property string|null $email
 * @property \Cake\I18n\FrozenDate|null $date_of_birth
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Contest $contest
 */
class Participant extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'contest_id' => true,
        'name' => true,
        'email' => true,
        'date_of_birth' => true,
        'created' => true,
        'modified' => true,
        'contest' => true
    ];
}
