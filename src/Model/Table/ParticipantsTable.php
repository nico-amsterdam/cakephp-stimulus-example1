<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
// use Cake\Form\Schema;
use Cake\Validation\Validation;
use Cake\Log\LogTrait;

/**
 * Participants Model
 *
 * @property \App\Model\Table\ContestsTable|\Cake\ORM\Association\BelongsTo $Contests
 *
 * @method \App\Model\Entity\Participant get($primaryKey, $options = [])
 * @method \App\Model\Entity\Participant newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Participant[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Participant|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Participant saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Participant patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Participant[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Participant findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ParticipantsTable extends Table
{
    use LogTrait;
    
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('participants');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Contests', [
            'foreignKey' => 'contest_id',
            'joinType' => 'INNER'
        ]);
    }

    public function whenNotMarkedForDeletion($context)
    {
        return (!isset($context['data']['mark_for_deletion']) || $context['data']['mark_for_deletion'] != 1);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $whenNotMarkedForDeletionFunc = [$this, 'whenNotMarkedForDeletion'];
        // requirePresence: uses array_key_exists, so null/empty is ok as well.
        $validator
            ->requirePresence(['id', 'name', 'email', 'date_of_birth']);

        $validator
            ->integer('id')
            ->allowEmptyString('id', __('Id is required'), 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 50)
            ->add(
                'name',
                'notBlank',
                [ // check if field contains more than whitespaces
                    'rule' => 'notBlank',
                    'message' => __('A participant name is required'),
                    'on' =>  $whenNotMarkedForDeletionFunc,
            ]
            )
            ->add('name', 'unique', [
                'rule' => 'validateUnique',
                'message' => __('This participant name has already been used'),
                'provider' => 'table',
                'on' =>  $whenNotMarkedForDeletionFunc,
            ]);

        $validator
            ->email('email', false, __('A valid email address is required'), $whenNotMarkedForDeletionFunc)
            ->add(
                'email',
                'notBlank',
                [ // check if field contains more than whitespaces
                    'rule' => 'notBlank',
                    'message' => __('An email address is required'),
                    'on' =>  $whenNotMarkedForDeletionFunc,
            ]
            )
            ->add('email', 'unique', [
                'rule' => 'validateUnique',
                'message' => __('This email address has already been used'),
                'provider' => 'table',
                'on' =>  $whenNotMarkedForDeletionFunc,
            ]);

        $validator
            ->date('date_of_birth')
            ->allowEmptyDate('date_of_birth')
            ->add('date_of_birth', 'format', [
                    'rule' => function ($value, $context) {
                        if (!Validation::date($value)) {
                            return false;
                        }
                        $year = gettype($value) === 'string' ? date_create($value)->format('Y') : ((int) $value['year']);
                        $nowYear = date('Y');
                        return $year >= $nowYear - 120 && $year <= $nowYear - 4;
                    },
                    'message' => __('Enter a valid date of birth'),
                ]);

        $validator
            ->boolean('mark_for_deletion')
            ->allowEmptyString('mark_for_deletion');

        $validator
            ->boolean('dynnew')
            ->allowEmptyString('dynnew');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['email']));
        $rules->add($rules->isUnique(['name']));
        $rules->add($rules->existsIn(['contest_id'], 'Contests'));

        return $rules;
    }
}
