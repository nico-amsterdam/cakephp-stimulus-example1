<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Form\Example1Form;
use Cake\Log\LogTrait;
use Cake\Event\Event;
use Cake\Http\Exception\UnauthorizedException;

/**
 * Example1 Controller
 *
 *
 */
class Example1Controller extends AppController
{
    use LogTrait;

    private function getNewParticipant(int $index, int $dynnew = 0)
    {
        return [
             'id' => $index,
             'name' => '',
             'email' => '',
             'date_of_birth' => '',
             'mark_for_deletion' => false,
             'dynnew' => $dynnew,  // dynamic new. true if participant is added by client-side code
           ];
    }

    /*
     * @return \App\Form\Example1Form $example1
     */
    private function newExample1Form(int $number_of_participants, string $action)
    {
        return new Example1Form(0, max(1, $number_of_participants), $action);
    }

    private function logRequestData(string $descr)
    {
        $this->log($descr . ': ' . print_r($this->request->getData(), true), 'debug');
    }

    private function endswith($string, $test)
    {
        $strlen = strlen($string);
        $testlen = strlen($test);
        if ($testlen > $strlen) {
            return false;
        }
        return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        // Enable POST to /example1/prize_snippet and /example1/add_participant_snippet:
        $this->Security->setConfig('unlockedActions', ['prizeSnippet', 'addParticipantSnippet']);

        // These hidden fields can change, because 'prize_snippet' can make them editable:
        $this->Security->setConfig('unlockedFields', ['contest.prize1', 'contest.prize2', 'contest.prize3']);

        if (isset($this->request) && $this->request->is('post')) {
            if (!$this->getRequest()->getSession()->check('_Token')) {
                $expiredMsg = __('Your session has expired due to inactivity.');
                // cannot redirect page for ajax requests
                if ($this->endswith($this->request->url, '_snippet')) {
                    // throw new UnauthorizedException($expiredMsg);
                    return $this->response->withHeader('X-HTTP-Error-Description', $expiredMsg . ' ' .  __('Reload the page to start a new session.'))->withStatus(401);
                }
                $this->Flash->error($expiredMsg);
                return $this->redirect(['action' => 'index']);
            }
            $dateType = $this->getDateType();
            $participants = $this->request->getData('contest.participants');
            $number_of_participants = count($participants);
            $dynnew =     (int) $this->request->getData('contest.participants.0.dynnew');

            $unlockBefore = $this->Security->getConfig('unlockFields') ?? [];
            for ($i = 0; $i < $number_of_participants; $i++) {
                $dynnew = (int) $participants[$i]['dynnew'];
                if ($dynnew === 1) {
                    $unlockBefore[] = 'contest.participants.' . $i . '.name';
                    $unlockBefore[] = 'contest.participants.' . $i . '.email';
                    $unlockBefore[] = 'contest.participants.' . $i . '.date_of_birth';
                    $unlockBefore[] = 'contest.participants.' . $i . '.dynnew';
                    $unlockBefore[] = 'contest.participants.' . $i . '.mark_for_deletion';
                }
            }
            $this->Security->setConfig('unlockedFields', $unlockBefore);
        }
    }

    private function participantsAreNotNewAnymore(array $data)
    {
        $newParticipants = [];
        $number_of_participants = count($this->request->getData('contest.participants'));
        if ($number_of_participants > 0) {
            foreach ($data['contest']['participants'] as $key => $participant) {
                $participant['dynnew'] = 0;
                $newParticipants[] = $participant;
            }
            $data['contest']['participants'] = $newParticipants;
        }
        return $data;
    }

    /*
     * @param array $data Data array.
     * @return \App\Form\Example1Form $example1
     */
    private function deleteMarkedAndNewParticipants(array $data)
    {
        $action = $this->request->getData('example1action');
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
        } elseif ($action == 'addParticipant') {
            $data['contest']['participants'][] = $this->getNewParticipant($number_of_participants);
            $number_of_participants += 1;
        }
        $example1 = $this->newExample1Form($number_of_participants, $action);
        $example1->setData($data);
        return $example1;
    }

    private function makeHiddenPrizesEmpty(array $data)
    {
        $number_of_prizes = $data['contest']['number_of_prizes'];
        if ($number_of_prizes < 3) {
            $data['contest']['prize3'] = '';
            if ($number_of_prizes < 2) {
                $data['contest']['prize2'] = '';
                if ($number_of_prizes < 1) {
                    $data['contest']['prize1'] = '';
                }
            }
        }
        return $data;
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
                // $this->log('Validation errors: ' . print_r( $example1->getErrors(), true), 'debug');
                $this->Flash->error(__('There was a problem submitting your form.'));
            }
            $session->write(['example1' => $this->makeHiddenPrizesEmpty(
                  $this->participantsAreNotNewAnymore($example1->getData())
                ) ,'action' => $example1->getAction()
                  ,'errors' => $example1->getErrors()
                ]);
            // POST and redirect pattern; to make sure that the browser-back works.
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
                      'number_of_prizes' => '1',
                      'prize1' => '',
                      'prize2' => '',
                      'prize3' => '',
                      'participants' => [
                         $this->getNewParticipant(0)
                      ],
                    ],
                ]);
            }
            $this->set('dateType', $this->getDateType());
            $this->set('example1', $example1);
            $this->set('autofocusIndex', (int) ($example1->getAction() == 'addParticipant' ? ($number_of_participants - 1) : -1));
        }
    }

    public function prizeSnippet()
    {
        // $this->logRequestData('prizeSnippet');
        $requestData = $this->request->getData();
        $example1 = $this->newExample1Form(0, 'updatePrizeRegion');
        $example1->setData($requestData);
        $this->set('example1', $example1);
        $this->render('/Element/Example1/Prize', 'ajax');
    }

    public function addParticipantSnippet()
    {
        // $this->logRequestData('addParticipantSnippet');
        $number_of_participants = count($this->request->getData('contest.participants'));
        $data = $this->request->getData();
        // add new participant
        $data['contest']['participants'][] = $this->getNewParticipant($number_of_participants++, 1);
        $example1 = $this->newExample1Form($number_of_participants, 'addParticipant');
        $example1->setData($data);
        $this->request = $this->request->withParsedBody($data);
        $this->set('dateType', $this->getDateType());
        $this->set('example1', $example1);
        $this->set('autofocusIndex', -1);
        $this->render('/Element/Example1/AddParticipant', 'ajax');
    }

    private function isInternetExplorer()
    {
        $isMSIE = strpos($this->request->getHeaderLine('User-Agent'), 'Trident/') !== false;
        return $isMSIE;
    }

    private function isSafariOnMacOS()
    {
        $userAgent =  $this->request->getHeaderLine('User-Agent');
        $isChrome  =  strpos($userAgent, 'Chrome/') !== false;
        $isSafari  = !$isChrome && strpos($userAgent, 'Safari/')   !== false;
        $isMacOS   = !$isChrome && strpos($userAgent, 'Macintosh') !== false;
        return $isMacOS && $isSafari;
    }

    private function getDateType()
    {
        return $this->isSafariOnMacOS() || $this->isInternetExplorer() ? 'date' : 'datepicker';
    }
}
