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

  public function beforeFilter(Event $event)
  {
     if (isset($this->request)) {
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
       $example1 = new Example1Form(0, 1); // $number_of_participants);
       if ($this->request->is('post')) {
          // $isValid = $example1->validate($this->request->getData());
         // echo '<pre>' . print_r($example1->getErrors(), true) . '</pre>';
          $example1->setData($this->request->getData());
          if ($example1->execute($this->request->getData())) {
             $this->Flash->success(__('Succes!'));
          } else {
             $this->Flash->error(__('There was a problem submitting your form.'));
          }
       }
       if ($this->request->is('get')) {
          $example1->setData([
             'contest' => [
               'name' => 'Best actor',
               'number_of_prices' => '2',
               'price1' => 'free tickets',
               'price2' => '',
               'price3' => '10 dollar',
               'participants' => [
                 [
                   'id' => 0,
                   'name' => 'John Doe',
                   'email' => 'john.doe@example.com',
                   'date_of_birth' => '2018-02-28',
                   'mark_for_deletion' => 0,
                   'dynnew' => 1
                 ],
                 [
                   'id' => 0,
                   'name' => 'John Doe',
                   'email' => 'john.doe@example.com',
                   'date_of_birth' => '2018-02-28',
                   'mark_for_deletion' => 0,
                   'dynnew' => 1
                 ]
               ]
              ],
          ]);
          // $this->log('GET DATA ' . print_r( $example1->getData('contest.participants'), true), 'debug');
          // $this->log('GET DATA2 ' . print_r( $example1->getData(), true), 'debug');
       }
       $this->Security->setConfig('unlockedFields', []);
   //    $this->set('contest', $example1->getContestForm());
   //    $this->set('participants', $example1->getData( 'contest.participants'));
       $this->set('example1', $example1);
    }

    public function getDateType() 
    {
        $isMSIE = strpos($this->request->getHeaderLine('User-Agent'), 'Trident/') !== false;
        return $isMSIE ? 'date' : 'datepicker';
    }

    /**
     * View method
     *
     * @param string|null $id Example id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $example1 = $this->Example1->get($id, [
            'contain' => []
        ]);

        $this->set('example', $example1);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $example1 = $this->Example1->newEntity();
        if ($this->request->is('post')) {
            $example1 = $this->Example1->patchEntity($example1, $this->request->getData());
            if ($this->Example1->save($example1)) {
                $this->Flash->success(__('The example has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The example could not be saved. Please, try again.'));
        }
        $this->set(compact('example'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Example id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $example1 = $this->Example1->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $example1 = $this->Example1->patchEntity($example1, $this->request->getData());
            if ($this->Example1->save($example1)) {
                $this->Flash->success(__('The example has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The example could not be saved. Please, try again.'));
        }
        $this->set(compact('example'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Example id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $example1 = $this->Example1->get($id);
        if ($this->Example1->delete($example1)) {
            $this->Flash->success(__('The example has been deleted.'));
        } else {
            $this->Flash->error(__('The example could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
