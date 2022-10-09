<?php

namespace App\Models\Subscribers\Repositories\Interfaces;

use Jsdecena\Baserepo\BaseRepositoryInterface;
use App\Models\Subscribers\Subscribe;
use Illuminate\Support\Collection;

interface SubscribeRepositoryInterface extends BaseRepositoryInterface
{
    public function listSubscribes(string $order = 'id', string $sort = 'desc', $except = []): Collection;

    public function createSubscribe(array $params): Subscribe;

    public function checkUniquePhone(string $phone): bool;

    public function findSubscribeById(int $id): Subscribe;

    public function findSubscriberByPhone(string $phone);

    public function findRegencyBySubscriber();

    public function findSubscriberByAddress(int $id): Collection;

    public function updateSubscribe(array $params): bool;

    public function deleteSubscribe(): bool;

    public function sendWhatsAppMessage(string $message, string $recipient);

    public function defaultMenu(string $name): string;

    public function listDefaultMenu(string $from, object $findNumber, string $body, object $districtRepo, object $fieldRepo): string;

    public function subscribersWhatsapp(): string;

    public function reportAdmin(int $daily, int $month, int $year, int $dailyReport): string;

    public function registerStep1(string $from, string $body, array $answerID, object $regencyRepo): string;

    public function listDistrictMenu(string $from, string $body, object $districtRepo, object $fieldRepo): string;

    public function responseReportMenu(string $answerID, string $from, string $body, object $findNumber, object $reportRepo, string $img = '');

    public function OptionReportMenu(string $answerID, string $from, string $body, object $findNumber, object $reportRepo): string;

    public function optionChangeInformation(string $answerID, string $from, string $body, object $findNumber, object $regencyRepo): string;

    public function filterFileMenu(string $nuMedia, string $ext): string;

    public function reportActionAction(string $answerID, string $from, string $body, object $findNumber, object $reportRepo): string;

    public function uploadImageFromWA(string $from, string $url, string $ext): string;
}
