<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Form\Example1Form;
use Cake\Log\LogTrait;
use Cake\Event\Event;

/**
 * Example1 Controller
 *
 *
 * @method \App\Model\Entity\Example[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class Example1Controller extends AppController
{
  use LogTrait;

  private function getNewParticipant(int $index) {
    return [
             'id' => $index,
             'name' => '',
             'email' => '',
             'date_of_birth' => '',
             'mark_for_deletion' => 0,
             'dynnew' => 0
           ];
  }

  private function newExample1Form(int $number_of_participants) {
     return new Example1Form(0, max(1,$number_of_participants));
  }

  public function beforeFilter(Event $event)
  {
     if (isset($this->request)) {
        $dateType = $this->getDateType();
        $number_of_participants = count($this->request->getData('contest.participants'));
        $dynnew =     (int) $this->request->getData('contest.participants.0.dynnew');
        $this->log('NEW ' . $this->request->getData('contest.participants.0.dynnew') . ' COUNT ' . $number_of_participants, 'debug');
        $this->log(print_r($this->request->getData() , true), 'debug');

        if ($number_of_participants > 0 && $dynnew === 1)
        {
           $this->log('UNLOCK', 'debug');
           $unlockBefore = $this->Security->getConfig('unlockFields') ?? [];
           // 'contest.participants.0.name',  'contest.participants.0.email',  'contest.participants.0.date_of_birth'
           $unlockBefore = array_merge($unlockBefore, []);
           $this->Security->setConfig('unlockedFields', $unlockBefore);
        } 
        else {
           $this->log('oeps ' . $number_of_participants, 'debug');
        } 

     }
     // $this->log(print_r($event, true), 'debug');
     parent::beforeFilter($event);
  }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
       $number_of_participants = count($this->request->getData('contest.participants'));
       $example1 = $this->newExample1Form($number_of_participants);
       if ($this->request->is('post')) {
          $action = $this->request->getData('action');
          $example1->setData($this->request->getData());
          if ($example1->execute($this->request->getData())) {
             $this->Flash->success(__('Succes!'));
             $data = $example1->getData();
             $newParticipants = [];
             if ($number_of_participants > 0) {
                foreach ($data['contest']['participants'] as $key => $participant) {
                   if ($participant['mark_for_deletion'] == 1) {
                      $number_of_participants -= 1;
                   } else {
                      $participant['id'] = count($newParticipants);
                      $newParticipants[] = $participant;
                   }
                }
                $data['contest']['participants'] = $newParticipants;
             }
             if ($number_of_participants <= 0) {
                // minimum one participant in the table. Add new record:
                $data['contest']['participants'] = [ $this->getNewParticipant(0) ];
                $number_of_participants = 1;
             } else if ($action == 'addParticipant') {
                $data['contest']['participants'][] = $this->getNewParticipant($number_of_participants);
                $number_of_participants += 1;
             }
             $example1 = $this->newExample1Form($number_of_participants);
             $example1->setData($data);
             // $this->set([$example1->getData()]);
             $this->request = $this->request->withParsedBody($data);
             $this->log('DATA3 ' . print_r( $example1->getData(), true), 'debug');
          } else {
             // echo '<pre>' . print_r($example1->getErrors(), true) . '</pre>';
             $this->log('Validation errors: ' . print_r( $example1->getErrors(), true), 'debug');
             $this->Flash->error(__('There was a problem submitting your form.'));
          }
       }
       if ($this->request->is('get')) {
          $example1->setData([
             'contest' => [
               'name' => '',
               'number_of_prices' => '1',
               'price1' => '',
               'price2' => '',
               'price3' => '',
               'participants' => [
                   $this->getNewParticipant(0)
               ],
              ],
          ]);
          // $this->log('GET DATA2 ' . print_r( $example1->getData(), true), 'debug');
       }
       $this->Security->setConfig('unlockedFields', []);
       $this->set('dateType', $this->getDateType());
       $this->set('example1', $example1);
    }

    public function getDateType() 
    {
        $isMSIE = strpos($this->request->getHeaderLine('User-Agent'), 'Trident/') !== false;
        return $isMSIE ? 'date' : 'datepicker';
    }

}
