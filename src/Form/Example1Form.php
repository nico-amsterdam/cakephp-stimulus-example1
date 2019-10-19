<?php
namespace App\Form;

use Cake\Form\Form;
use Cake\Form\Schema;
use Cake\Validation\Validator;
use Cake\Validation\Validation;
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
  

    public function __construct(int $participants_offset, int $number_of_participants, string $action)
    {
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
        $schema->addField('contest.name', 'string');
        $schema->addField('contest.number_of_prizes', 'int');
        $schema->addField('contest.prize1', 'string');
        $schema->addField('contest.prize2', 'string');
        $schema->addField('contest.prize3', 'string');
        for ($i = $this->participants_offset, $end_at = $this->participants_offset + $this->number_of_participants; $i < $end_at; $i++) {
            $prefix = 'contest.participants.' . $i . '.';
            $schema->addField($prefix . 'name', 'string');
            $schema->addField($prefix . 'email', 'email');
            $schema->addField($prefix . 'date_of_birth', 'date');
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
        return $validator->addNested('contest', $this->getContestValidator());
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
           requirePresence(['name', 'number_of_prizes'])->
           notEmpty('name', __('A contest name is required'))->
           notEmpty('number_of_prizes', __('Number of prizes is required'))->
           notEmpty('prize1', __('First prize is required'), function ($context) {
               return ($context['data']['number_of_prizes'] >= 1);
           })->
           notEmpty('prize2', __('First prize is required'), function ($context) {
               return ($context['data']['number_of_prizes'] >= 2);
           })->
           notEmpty('prize3', __('First prize is required'), function ($context) {
               return ($context['data']['number_of_prizes'] >= 3);
           })->
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
           notEmpty('name', __('A participant name is required'), function ($context) {
               return !($context['data']['mark_for_deletion'] == 1);
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
