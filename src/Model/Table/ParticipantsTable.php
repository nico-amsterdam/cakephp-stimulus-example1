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

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->requirePresence(['id', 'name', 'email', 'date_of_birth']);

        $validator
            ->integer('id')
            ->allowEmptyString('id', 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 50)
            ->allowEmptyString('name', function ($context) {
                 $this->log('CONTEXT ' . print_r($context['data'], true), 'debug');
                 return (!isset($context['data']['mark_for_deletion']) || $context['data']['mark_for_deletion'] == 1); 
              })
            // ->notEmpty('name', __('A participant name is required'), function($context) {
            //      return !($context['data']['mark_for_deletion'] == 1); 
            //   })
            ->add('name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->email('email')
            ->allowEmptyString('email')
            ->add('email', 'format', [
                     'rule' => 'email',
                     'message' => __('A valid email address is required'),
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
                        // $this->log('COMPARE '. $year . ' with ' . $nowYear, 'debug');

                        return $year >= $nowYear - 120 && $year <= $nowYear - 4;
                    },
                    'message' => __('Enter a valid date of birth'),
                ]);

        // $validator
        //     ->add('mark_for_deletion', 'boolean', [
        //             'rule' => 'boolean'
        //         ]);

        $validator
            ->add('dynnew', 'boolean', [
                    'rule' => 'boolean'
                ]);

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
