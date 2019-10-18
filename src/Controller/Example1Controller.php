<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Table\ContestsTable;
use Cake\Log\LogTrait;
use Cake\Event\Event;
use Cake\Http\Exception\UnauthorizedException;

/**
 * Example1 Controller
 *
 * @property \App\Model\Table\ContestsTable $Contests
 *
 */
class Example1Controller extends AppController
{
    use LogTrait;

    private function getNewParticipant(int $dynnew = 0)
    {
        return [
             'name' => '',
             'email' => '',
             'date_of_birth' => '',
             'mark_for_deletion' => false,
             'dynnew' => $dynnew,  // dynamic new. true if participant is added by client-side code
           ];
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
        // $this->logRequestData('beforeFilter');
   
        parent::beforeFilter($event);
   
        // Enable POST to /example1/prize_snippet and /example1/add_participant_snippet:
        $this->Security->setConfig('unlockedActions', ['prizeSnippet', 'addParticipantSnippet']);

        // These hidden fields can change, because 'prize_snippet' can make them editable:
        $this->Security->setConfig('unlockedFields', ['prize1', 'prize2', 'prize3']);

        if (isset($this->request) && $this->request->is(['post', 'put'])) {
            if (!$this->getRequest()->getSession()->check('_Token')) {
                $expiredMsg = __('Your session has expired due to inactivity.');
                // cannot redirect page for ajax requests
                if ($this->endswith($this->request->url, '_snippet')) {
                    // throw new UnauthorizedException($expiredMsg);
                  return $this->response->withHeader('X-HTTP-Error-Description', 
                         $expiredMsg . ' ' .  __('Reload the page to start a new session.'))->withStatus(401);
                }
                $this->Flash->error($expiredMsg);
                return $this->redirect(['action' => 'index']);
            }
            $dateType = $this->getDateType();
            $participants = $this->request->getData('participants');
            $number_of_participants = count($participants);
            $dynnew = 0;

            $unlockBefore = $this->Security->getConfig('unlockFields') ?? [];
            for ($i = 0; $i < $number_of_participants; $i++) {
                $dynnew = (int) $participants[$i]['dynnew'];
                if ($dynnew === 1) {
                    $unlockBefore[] = 'participants.' . $i . '.id';
                    $unlockBefore[] = 'participants.' . $i . '.name';
                    $unlockBefore[] = 'participants.' . $i . '.email';
                    $unlockBefore[] = 'participants.' . $i . '.date_of_birth';
                    $unlockBefore[] = 'participants.' . $i . '.dynnew';
                    $unlockBefore[] = 'participants.' . $i . '.mark_for_deletion';
                }
            }
            $this->Security->setConfig('unlockedFields', $unlockBefore);
        }
    }

    private function participantsAreNotNewAnymore($data)
    {
        foreach ($data['participants'] as $key => $participant) {
            $data['participants'][$key]['dynnew'] = 0;
        }
        return $data;
    }

    /*
     * @param \App\Model\Entity\Contest contest.
     * @param data.
     * @return \App\Model\Entity\Contest contest
     */
    private function deleteMarkedParticipants($contest, $data)
    {
        $number_of_participants = count($data['participants']);
        $participants = [];

        if ($number_of_participants > 0) {
            foreach ($contest['participants'] as $key => $participant) {
                if ($data['participants'][$key]['mark_for_deletion'] != 1) {
                    $participants[] = $contest['participants'][$key];
                }
            }
        }
        $newContest = clone $contest;
        $newContest['participants'] = $participants;
        return $newContest;
    }

    private function makeHiddenPrizesEmpty($data)
    {
        $number_of_prizes = $data['number_of_prizes'];
        if ($number_of_prizes < 3) {
            $data['prize3'] = '';
            if ($number_of_prizes < 2) {
                $data['prize2'] = '';
                if ($number_of_prizes < 1) {
                    $data['prize1'] = '';
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
        $this->loadModel('Contests');
        $contest = null;
        $action = $this->request->getData('example1action');
        $session = $this->getRequest()->getSession();

        if ($this->request->is(['post', 'put'])) {
            $id = 1;
            $origContest = $this->Contests->get($id, [
               'contain' => ['Participants']
            ]);

            $origData = $this->request->getData();

            $contest = $this->Contests->patchEntity($origContest, $origData, [
               'associated' => ['Participants']
            ]);

            // remove prizes that are not shown anymore
            $contest = $this->makeHiddenPrizesEmpty($contest);

            $contest_after_removal = $this->deleteMarkedParticipants($contest, $origData);

            if ($this->Contests->save($contest_after_removal)) {
                $this->Flash->success(__('The contest has been saved.'));
                $session->delete('contest');
                $session->delete('data');
                $session->write('action', $action);
            } else {
                // echo '<pre>' . print_r($contest_after_removal->getErrors(), true) . '</pre>';
                $this->Flash->error(__('The contest could not be saved. Please, try again.'));

                $noDynnew = $this->participantsAreNotNewAnymore($origData);
                $session->write([ 'data'    => $noDynnew,
                              'contest' => $origContest,
                              'action'  => $action,
                ]);
            }
            // use post and redirect to avoid problems with browser-back button
            return $this->redirect(['action' => 'index']);
        } elseif ($this->request->is('get')) {
            if ($session->check('contest')) {
                $contest = $session->read('contest');
                if ($session->check('data')) {
                    $origData = $session->read('data');
                    $this->request = $this->request->withParsedBody($origData);
                }
            } else {
                $id = 1;
                $contest = $this->Contests->get($id, [
                   'contain' => ['Participants']
                ]);
            }

            $action  = $session->read('action');
            $number_of_participants = count($contest['participants']);
            if ($number_of_participants <= 0) {
                // minimum one participant in the table. Add new record:
                $contest['participants'] = [ $this->getNewParticipant() ];
                $number_of_participants = 1;
            } elseif ($action == 'addParticipant') {
                $contest['participants'][] = $this->getNewParticipant();
                $number_of_participants += 1;
            }
            $this->set('contest', $contest);
            $this->set('dateType', $this->getDateType());
            $this->set('autofocusIndex', (int) ($action == 'addParticipant' ? ($number_of_participants - 1) : -1));
        }
    }

    public function prizeSnippet()
    {
        // $this->logRequestData('prizeSnippet');
        $requestData = $this->request->getData();
        $this->set('contest', $requestData);
        $this->render('/Element/Example1/Prize', 'ajax');
    }

    public function addParticipantSnippet()
    {
        // $this->logRequestData('addParticipantSnippet');
        $number_of_participants = count($this->request->getData('participants'));
        $contest = $this->request->getData();
        // add new participant
        $number_of_participants++;
        $contest['participants'][] = $this->getNewParticipant(1);
        $this->request = $this->request->withParsedBody($contest);
        $this->set('dateType', $this->getDateType());
        $this->set('contest', $contest);
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
        $userAgent = $this->request->getHeaderLine('User-Agent');
        $isChrome = strpos($userAgent, 'Chrome/') !== false;
        $isSafari = !$isChrome && strpos($userAgent, 'Safari/')   !== false;
        $isMacOS  = strpos($userAgent, 'Macintosh') !== false;
        return $isMacOS && $isSafari;
    }

    private function getDateType()
    {
        return $this->isSafariOnMacOS() || $this->isInternetExplorer() ? 'date' : 'datepicker';
    }
}
