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

  private function getNewParticipant(int $index, int $dynnew = 0) {
    return [
//             'id' => $index,
             'name' => '',
             'email' => '',
             'date_of_birth' => '',
             'mark_for_deletion' => false,
             'dynnew' => $dynnew,
           ];
  }

  /*
   * @return \App\Form\Example1Form $example1
   */
  private function newExample1Form(int $number_of_participants, string $action) {
     return new Example1Form(0, max(1,$number_of_participants), $action);
  }

  private function logRequestData(string $descr) {
     $this->log($descr . ': ' . print_r($this->request->getData() , true), 'debug');
  }

  public function beforeFilter(Event $event)
  {
     parent::beforeFilter($event);
     // Enable POST to /example1/price_snippet and /example1/add_participant_snippet:
     $this->Security->setConfig('unlockedActions', ['priceSnippet', 'addParticipantSnippet']);
     // These hidden fields can change, because 'price_snippet' can make them editable:
     $this->Security->setConfig('unlockedFields', ['contest.price1', 'contest.price2', 'contest.price3']);
     if (isset($this->request) && $this->request->is('post'))
     {
        if (!$this->getRequest()->getSession()->check('_Token')) {
           $this->Flash->error(__('Your session has expired due to inactivity.'));
           return $this->redirect(['action' => 'index']);
        }
        $dateType = $this->getDateType();
        $participants = $this->request->getData('contest.participants');
        $number_of_participants = count($participants);
        $dynnew =     (int) $this->request->getData('contest.participants.0.dynnew');
        $this->logRequestData('beforeFilter');

          $unlockBefore = $this->Security->getConfig('unlockFields') ?? [];
          $this->log('unlockBefore1: ' . print_r($unlockBefore, true), 'debug');
          for ($i = 0; $i < $number_of_participants; $i++)
          {
              $this->log('NEW ' . $i . '=' . print_r($participants[$i], true), 'debug');
              $dynnew = (int) $participants[$i]['dynnew'];
              if ($dynnew === 1) {
                $unlockBefore[] = 'contest.participants.' . $i . '.name';
                $unlockBefore[] = 'contest.participants.' . $i . '.email';
                $unlockBefore[] = 'contest.participants.' . $i . '.date_of_birth';
                $unlockBefore[] = 'contest.participants.' . $i . '.dynnew';
                $unlockBefore[] = 'contest.participants.' . $i . '.mark_for_deletion';
              }
          }
          $this->log('unlockBefore2: ' . print_r($unlockBefore, true), 'debug');
          $this->Security->setConfig('unlockedFields', $unlockBefore);
          $this->log('No participants: ' . $number_of_participants, 'debug');
     }
     // $this->log(print_r($event, true), 'debug');
  }

  /*
   * @param array $data Data array.
   * @return \App\Form\Example1Form $example1
   */
  private function deleteMarkedAndNewParticipants($data) {
     $action = $this->request->getData('example1action');
     $newParticipants = [];
     $number_of_participants = count($this->request->getData('contest.participants'));
     if ($number_of_participants > 0) {
        foreach ($data['contest']['participants'] as $key => $participant) {
           if ($participant['mark_for_deletion'] == 1) {
              $number_of_participants -= 1;
           } else {
              $participant['id'] = count($newParticipants);
              $participant['dynnew'] = 0;
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
        $action = $this->request->getData('example1action');
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
     $this->Security->setConfig('unlockedFields', []);  // TODO: reset unlocked because new participants are not locked anymore after submit?
     $this->set('dateType', $this->getDateType());
     $this->set('example1', $example1);
     $this->set('autofocusIndex', (int) ($example1->getAction() == 'addParticipant' ? ($number_of_participants - 1) : -1));
  }

  public function priceSnippet() {
     $this->logRequestData('priceSnippet');
     $requestData = $this->request->getData();
     $example1 = $this->newExample1Form(0, 'updatePriceRegion');
     $example1->setData($requestData);
     $this->set('example1', $example1);
     $this->render('/Element/Example1/Price', 'ajax');
  }

  public function addParticipantSnippet() {
     $this->logRequestData('addParticipantSnippet');
     $number_of_participants = count($this->request->getData('contest.participants'));
     $data = $this->request->getData();
     // add new participant
     $data['contest']['participants'][] = $this->getNewParticipant($number_of_participants++, 1);
     $example1 = $this->newExample1Form($number_of_participants, 'addParticipant');
     $example1->setData($data);
     $this->log('DATA WORDT: ' . print_r( $data, true), 'debug');
     $this->request = $this->request->withParsedBody($data);
     $this->set('dateType', $this->getDateType());
     $this->set('example1', $example1);
     $this->set('autofocusIndex', -1);
     $this->render('/Element/Example1/AddParticipant', 'ajax');
  }

  private function isInternetExplorer() {
      $isMSIE = strpos($this->request->getHeaderLine('User-Agent'), 'Trident/') !== false;
      return $isMSIE;
  }

  private function getDateType() 
  {
      return $this->isInternetExplorer() ? 'date' : 'datepicker';
  }
}
