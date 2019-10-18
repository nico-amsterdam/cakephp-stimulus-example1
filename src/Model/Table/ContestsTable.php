<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Contests Model
 *
 * @property \App\Model\Table\ParticipantsTable|\Cake\ORM\Association\HasMany $Participants
 *
 * @method \App\Model\Entity\Contest get($primaryKey, $options = [])
 * @method \App\Model\Entity\Contest newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Contest[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Contest|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Contest saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Contest patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Contest[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Contest findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ContestsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('contests');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Participants', [
            'foreignKey' => 'contest_id',
            'saveStrategy' => 'append' // merge participants entered and saved by other users
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
            ->integer('id')
            ->allowEmptyString('id',  __('Id is required'), 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 50)
            ->requirePresence('name')
            // ->allowEmptyString('name', false)
            ->notEmpty('name', __('A contest name is required'))
            ->add('name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator
            ->integer('number_of_prizes')
            ->requirePresence('number_of_prizes')
            // ->allowEmptyString('number_of_prizes', false)
            ->notEmpty('number_of_prizes', __('Number of prizes is required'));

        $validator
            ->scalar('prize1')
            ->maxLength('prize1', 255)
            // ->allowEmptyString('prize1')
            ->notEmpty('prize1', __('First prize is required'), function($context) {
                return ($context['data']['number_of_prizes'] >= 1); 
              });

        $validator
            ->scalar('prize2')
            ->maxLength('prize2', 255)
            // ->allowEmptyString('prize2')
            ->notEmpty('prize2', __('Second prize is required'), function($context) {
                return ($context['data']['number_of_prizes'] >= 2); 
              });

        $validator
            ->scalar('prize3')
            ->maxLength('prize3', 255)
            // ->allowEmptyString('prize3')
            ->notEmpty('prize3', __('Third prize is required'), function($context) {
                return ($context['data']['number_of_prizes'] >= 3); 
              });

        // addNestedMany('participants', $this->getParticipantValidator());


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
        $rules->add($rules->isUnique(['name']));

        return $rules;
    }
}
