<?php
namespace Twitterbot\Lib;

class Tweet extends Base
{
    public function post($aTweets = array())
    {
        if (!$aTweets) {
            $this->logger->write(3, 'Nothing to tweet.');
            $this->logger->output('Nothing to tweet.');

            return false;
        }

        $aTweets = (is_array($aTweets) ? $aTweets : array($aTweets));
        if (!empty($this->aMediaIds)) {
            $this->aMediaIds = (is_array($this->aMediaIds) ? $this->aMediaIds : array($this->aMediaIds));
        }

        foreach ($aTweets as $sTweet) {
            if (!empty($this->aMediaIds)) {
                //TODO: why array_shift for just the first one? twitter API supports up to 4 attachments
                $sMediaId = array_shift($this->aMediaIds);
                $this->logger->output('Tweeting: [%dch] %s (with attachment)', strlen($sTweet), utf8_decode($sTweet));
                $oRet = $this->oTwitter->post('statuses/update', array('status' => $sTweet, 'trim_users' => true, 'media_ids' => $sMediaId));
            } else {
                $this->logger->output('Tweeting: [%dch] %s', strlen($sTweet), utf8_decode($sTweet));
                $oRet = $this->oTwitter->post('statuses/update', array('status' => $sTweet, 'trim_users' => true));
            }
            if (isset($oRet->errors)) {
                $this->logger->write(2, sprintf('Twitter API call failed: statuses/update (%s)', $oRet->errors[0]->message), array('tweet' => $sTweet));
                $this->logger->output('- Error: %s (code %s)', $oRet->errors[0]->message, $oRet->errors[0]->code);

                return false;
            }
        }

        return true;
    }

    public function setMedia($aMediaIds)
    {
        $this->aMediaIds = $aMediaIds;

        return $this;
    }
}
