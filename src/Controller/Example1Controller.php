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

  /*
   * @return \App\Form\Example1Form $example1
   */
  private function newExample1Form(int $number_of_participants, string $action) {
     return new Example1Form(0, max(1,$number_of_participants), $action);
  }

  public function beforeFilter(Event $event)
  {
     if (isset($this->request) && $this->request->is('post'))
     {
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
        else
        {
           $this->log('oeps ' . $number_of_participants, 'debug');
        } 
     }
     // $this->log(print_r($event, true), 'debug');
     parent::beforeFilter($event);
  }

  /*
   * @param array $data Data array.
   * @return \App\Form\Example1Form $example1
   */
  private function deleteMarkedAndNewParticipants($data) {
     $action = $this->request->getData('action');
     $newParticipants = [];
     $number_of_participants = count($this->request->getData('contest.participants'));
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
     $example1 = $this->newExample1Form($number_of_participants, $action);
     $example1->setData($data);
     return $example1;
  }

  /**
   * Index method
   *
   * @return \Cake\Http\Response|void
   */
  public function index()
  {
     if ($this->request->is('post')) {
        $action = $this->request->getData('action');
        $number_of_participants = count($this->request->getData('contest.participants'));
        $example1 = $this->newExample1Form($number_of_participants, $action);
        $example1->setData($this->request->getData());
        $session = $this->getRequest()->getSession();
        if ($example1->execute($this->request->getData())) {
           if ($action == 'updateNoSave') {
              $this->Flash->success(__('Succes!'));
           }
           $example1 = $this->deleteMarkedAndNewParticipants($example1->getData());
        } else {
           // echo '<pre>' . print_r($example1->getErrors(), true) . '</pre>';
           $this->log('Validation errors: ' . print_r( $example1->getErrors(), true), 'debug');
           $this->Flash->error(__('There was a problem submitting your form.'));
        }
        $session->write(['example1' => $example1->getData()
                        ,'action' => $example1->getAction()
                        ,'errors' => $example1->getErrors()
                        ]);
        // POST and redirect pattern; make sure browser-back works.
        return $this->redirect(['action' => 'index']);
     }
     if ($this->request->is('get')) {
        $session = $this->getRequest()->getSession();
        if ($session->check('example1')) {
          $data = $session->read('example1');
          $number_of_participants = count($data['contest']['participants']);
          $example1 = $this->newExample1Form($number_of_participants, $session->read('action'));
          $example1->setData($data);
          $example1->setErrors($session->read('errors'));
        } else {
          $number_of_participants = 1;
          $example1 = $this->newExample1Form($number_of_participants, 'init');
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
        }
     }
     $this->Security->setConfig('unlockedFields', []);
     $this->set('dateType', $this->getDateType());
     $this->set('example1', $example1);
     $this->set('autofocusIndex', (int) (($this->canAutofocus() && $example1->getAction() == 'addParticipant') ? ($number_of_participants - 1) : -1));
  }

  private function isInternetExplorer() {
      $isMSIE = strpos($this->request->getHeaderLine('User-Agent'), 'Trident/') !== false;
      return $isMSIE;
  }

  private function isEdge() {
      $isEdge = strpos($this->request->getHeaderLine('User-Agent'), ' Edge/') !== false;
      return $isEdge;
  }

  private function getDateType() 
  {
      return $this->isInternetExplorer() ? 'date' : 'datepicker';
  }

  private function canAutofocus() {
    return !false;  // !$this->isInternetExplorer() and !$this->isEdge();
  }
}
