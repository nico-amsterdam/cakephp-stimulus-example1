<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Contest Entity
 *
 * @property int $id
 * @property string $name
 * @property int $number_of_prizes
 * @property string|null $prize1
 * @property string|null $prize2
 * @property string|null $prize3
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Participant[] $participants
 */
class Contest extends Entity
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
        'name' => true,
        'number_of_prizes' => true,
        'prize1' => true,
        'prize2' => true,
        'prize3' => true,
        'created' => true,
        'modified' => true,
        'participants' => true
    ];
}
