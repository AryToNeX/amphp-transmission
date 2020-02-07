/*
   Copyright 2020 AryToNeX

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/

<?php

namespace AryToNeX\Transmission;

use Amp\Http\Client\HttpClientBuilder;
use Amp\Http\Client\Request;
use Amp\Http\Client\Response;
use function Amp\call;
use Amp\Promise;

class Transmission{

    private $host;
    private $port;
    private $endpoint;
    private $sessionId;

    public function __construct(string $host = "127.0.0.1", int $port = 9091, string $endpoint = "transmission/rpc/"){
        $this->host = $host;
        $this->port = $port;
        $this->endpoint = $endpoint;
        $this->uri = "http://" . $this->host . ":" . $this->port . "/" . $this->endpoint;
    }

    public function async_request($method, $arguments) : \Generator{
        $client = HttpClientBuilder::buildDefault();
        $request = new Request($this->uri, "POST");
        $request->setHeader("X-Transmission-Session-Id", $this->sessionId ?? "");
        $request->setBody(json_encode(["method" => $method, "arguments" => $arguments]));

        /** @var Response $response */
        $response = yield $client->request($request);
        if ($response->getStatus() === 409){
            $this->sessionId = $response->getHeader("x-transmission-session-id");
            return yield \Amp\call([$this, "async_request"], $method, $arguments);
        }

        $body = yield $response->getBody()->buffer();
        return json_decode($body, true);
    }

    public function addTorrent($arguments) : Promise{
        return call([$this, "async_request"], "torrent-add", $arguments);
    }

    public function removeTorrent($arguments) : Promise{
        return call([$this, "async_request"], "torrent-remove", $arguments);
    }

    public function startTorrent($arguments) : Promise{
        return call([$this, "async_request"], "torrent-start", $arguments);
    }

    public function startTorrentNow($arguments) : Promise{
        return call([$this, "async_request"], "torrent-start-now", $arguments);
    }

    public function stopTorrent($arguments) : Promise{
        return call([$this, "async_request"], "torrent-stop", $arguments);
    }

    public function verifyTorrent($arguments) : Promise{
        return call([$this, "async_request"], "torrent-verify", $arguments);
    }

    public function reannounceTorrent($arguments) : Promise{
        return call([$this, "async_request"], "torrent-reannounce", $arguments);
    }

    public function getTorrents(array $data = []) : Promise{
        if(!isset($data["fields"]))
            $data["fields"] = [
                "activityDate",
                "addedDate",
                "bandwidthPriority",
                "comment",
                "corruptEver",
                "creator",
                "dateCreated",
                "desiredAvailable",
                "doneDate",
                "downloadDir",
                "downloadedEver",
                "downloadLimit",
                "downloadLimited",
                "editDate",
                "error",
                "errorString",
                "eta",
                "etaIdle",
                "files",
                "fileStats",
                "hashString",
                "haveUnchecked",
                "haveValid",
                "honorsSessionLimits",
                "id",
                "isFinished",
                "isPrivate",
                "isStalled",
                "labels",
                "leftUntilDone",
                "magnetLink",
                "manualAnnounceTime",
                "maxConnectedPeers",
                "metadataPercentComplete",
                "name",
                "peer-limit",
                "peers",
                "peersConnected",
                "peersFrom",
                "peersGettingFromUs",
                "peersSendingToUs",
                "percentDone",
                "pieces",
                "pieceCount",
                "pieceSize",
                "priorities",
                "queuePosition",
                "rateDownload",
                "rateUpload",
                "recheckProgress",
                "secondsDownloading",
                "secondsSeeding",
                "seedIdleLimit",
                "seedIdleMode",
                "seedRatioLimit",
                "seedRatioMode",
                "sizeWhenDone",
                "startDate",
                "status",
                "trackers",
                "trackerStats",
                "totalSize",
                "torrentFile",
                "uploadedEver",
                "uploadLimit",
                "uploadLimited",
                "uploadRatio",
                "wanted",
                "webseeds",
                "webseedsSendingToUs",
            ];
        return call([$this, "async_request"], "torrent-get", $data);
    }

}
