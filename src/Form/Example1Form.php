<?php
namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;
use Cake\Log\LogTrait;

/**
 * Example1 Form.
 */
class Example1Form extends Form
{
    use LogTrait;

    private $action;
    protected $number_of_participants = 0;
    protected $participants_offset = 0;
  

    function __construct(int $participants_offset, int $number_of_participants, $action) {
       $this->participants_offset    = $participants_offset;
       $this->number_of_participants = $number_of_participants;
       $this->action = $action;
       parent::__construct(null);
    }

    /**
     * Builds the schema for the modelless form
     *
     * @param \Cake\Form\Schema $schema From schema
     * @return \Cake\Form\Schema
     */
    protected function _buildSchema(Schema $schema)
    {
       $schema->addField('contest.name',   'string');
       $schema->addField('contest.number_of_prices', 'int');
       $schema->addField('contest.price1', 'string');
       $schema->addField('contest.price2', 'string');
       $schema->addField('contest.price3', 'string');
       for ($i = $this->participants_offset, $end_at = $this->participants_offset + $this->number_of_participants; $i < $end_at; $i++) {
           $prefix = 'contest.participants.' . $i . '.';
           $schema->addField($prefix . 'name',  'string');
           $schema->addField($prefix . 'email', 'email');
           $schema->addField($prefix . 'date_of_birth',     'date');
           $schema->addField($prefix . 'mark_for_deletion', 'boolean');
           $schema->addField($prefix . 'dynnew', 'boolean');
       }
       return $schema;
    }

    /**
     * Form validation builder
     *
     * @param \Cake\Validation\Validator $validator to use against the form
     * @return \Cake\Validation\Validator
     */
    protected function _buildValidator(Validator $validator)
    {
      return $validator->
         addNested('contest', $this->getContestValidator());
    }

    /**
     * Form validation builder
     *
     * @param \Cake\Validation\Validator $validator to use against the form
     * @return \Cake\Validation\Validator
     */
    protected function getContestValidator()
    {
        $contestValidator = new Validator();
        return $contestValidator->
           requirePresence(['name', 'number_of_prices', 'price1'])->
           notEmpty('name', __('A contest name is required'))->
           notEmpty('number_of_prices', __('Number of prices is required'))->
           addNestedMany('participants', $this->getParticipantValidator());
    }

    /**
     * Form validation builder
     *
     * @return \Cake\Validation\Validator
     */
    protected function getParticipantValidator()
    {
        $participantValidator = new Validator();
        return $participantValidator->
           requirePresence(['name', 'email', 'date_of_birth'])->
           notEmpty('name', __('A participant name is required'), function($context) {
               return !($context['data']['mark_for_deletion'] == 1); 
           })->
           allowEmptyString('name', function ($context) {
               // $this->log('CONTEXT '. print_r($context['data'], true), 'debug');
               return ($context['data']['mark_for_deletion'] == 1); 
           })->
           allowEmptyString('email')->
           add('email', 'format', [
             'rule' => 'email',
             'message' => __('A valid email address is required'),
           ])->
           add('mark_for_deletion', 'boolean', [
             'rule' => 'boolean'
           ])->
           add('dynnew', 'boolean', [
             'rule' => 'boolean'
           ])->
           allowEmptyDate('date_of_birth')->
           add('date_of_birth', 'format', [
             'rule' => 'date',
             'message' => __('A valid date of birth is required'),
           ]);
    }

    public function getAction()
    {
      return $this->action;
    }

    /**
     * Defines what to execute once the From is being processed
     *
     * @param array $data Form data.
     * @return bool
     */
    protected function _execute(array $data)
    {
        return true;
    }
}
