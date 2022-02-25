<?php

namespace App\Controller;

use App\Entity\BankOperation;
use App\Entity\User;
use App\Form\BankOperationModalType;
use App\Form\BudgetSearchType;
use App\Form\BudgetUploadType;
use App\Repository\BankOperationRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/budget")]
class BankOperationController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var BankOperationRepository
     */
    private BankOperationRepository $bankOperationRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param BankOperationRepository $bankOperationRepository
     */
    public function __construct(
        EntityManagerInterface  $entityManager,
        BankOperationRepository $bankOperationRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->bankOperationRepository = $bankOperationRepository;
    }

    #[Route("/", name: "bank_operation_index", methods: ["GET", "POST"])]
    public function index(Request $request, PaginatorInterface $paginator, FormFactoryInterface $formFactory): Response
    {
        $searchForm = $this->createForm(BudgetSearchType::class);
        $searchForm->handleRequest($request);

        $searchValues = [];

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $searchValues = $searchForm->getData();
        }

        $bankOperationsQuery = $this->bankOperationRepository->getBankOperationsQuery($searchValues);

        $paginatedBankOperations = $paginator->paginate(
            $bankOperationsQuery,
            $request->query->getInt('page', 1),
            50
        );

        $bankOperationForms = [];

        $submittedBankOperationForm = false;
        foreach ($paginatedBankOperations as $bankOperation) {
            $form = $formFactory->createNamed(
                sprintf('bank_operation_form_%d', $bankOperation->getId()),
                BankOperationModalType::class,
                $bankOperation
            );
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $submittedBankOperationForm = true;
                $bankOperationValues = $form->getData();
                $this->entityManager->persist($bankOperationValues);
            }
            $bankOperationForms[$bankOperation->getId()] = $form->createView();
        }

        if ($submittedBankOperationForm) {
            $this->entityManager->flush();
            return $this->redirectToRoute('bank_operation_index', $request->query->all());
        }

        return $this->render('bank_operation/index.html.twig', [
            'controller_name' => 'BankOperationController',
            'bankOperations' => $paginatedBankOperations,
            'bankOperationForms' => $bankOperationForms,
            'searchForm' => $searchForm->createView(),
            'searchValues' => $searchValues
        ]);
    }

    #[Route("/upload", name: "bank_operation_upload", methods: ["GET", "POST"])]
    public function upload(Request $request)
    {
        $budgetUploadForm = $this->createForm(BudgetUploadType::class);
        $budgetUploadForm->handleRequest($request);

        $bankOperationsUIDS = $this->bankOperationRepository->getUniqIdsList();

        if ($budgetUploadForm->isSubmitted() && $budgetUploadForm->isValid()) {

            /** @var UploadedFile $budgetFile */
            $budgetFile = $budgetUploadForm['budgetFile']->getData();
            if ($budgetFile) {
                $budgetFileContent = file_get_contents($budgetFile->getRealPath());
                $bankOperationsArray = $this->handleBudgetFileContent($budgetFileContent);

                /** @var User $user */
                $user = $this->getUser();
                foreach ($bankOperationsArray as $bankOperation) {
                    if (!$bankOperationsUIDS->contains($bankOperation['uniqId'])) {
                        $bankOperationItem = $this->createBankOperation($bankOperation, $user);
                        $bankOperationsUIDS->add($bankOperation['uniqId']);
                        $bankOperations[] = $bankOperationItem;
                    }
                }
                $this->entityManager->flush();

                return $this->redirectToRoute('bank_operation_index');
            }
        }

        return $this->render('bank_operation/upload.html.twig', [
            'budgetUploadForm' => $budgetUploadForm->createView()
        ]);
    }

    /**
     * @param $budgetFileContent
     * @return array
     */
    private function handleBudgetFileContent($budgetFileContent): array
    {
        $csv = str_getcsv($budgetFileContent, ';');
        $csv = array_map('trim', $csv);
        $csv = array_map('utf8_encode', $csv);
        $csv = array_slice($csv, 3);

        $csvLines = array();
        $csvLength = count($csv);
        for ($i = 1; $i <= $csvLength / 4; $i++) {
            $csvLine = array_slice($csv, 0 + ($i * 4), 4);
            if (count($csvLine) !== 4) {
                continue;
            }
            $datetime = DateTime::createFromFormat('d/m/Y', $csvLine[0]);
            $rawLabel = $csvLine[1];
            $realLabel = trim(preg_replace('/\s+/', ' ', str_replace(PHP_EOL, ' ', substr($rawLabel, strpos($rawLabel, PHP_EOL)))));
            $operationKind = trim(substr($rawLabel, 0, strpos($rawLabel, PHP_EOL)));

            $debit = $this->stringToFloat($csvLine[2]);
            $credit = $this->stringToFloat($csvLine[3]);
            $amount = $debit ? -$debit : $credit;

            $lineArray = [
                'uniqId' => $this->getOperationUniqId($datetime, $realLabel, $amount),
                'operationDate' => $datetime,
                'label' => $realLabel,
                'operationKind' => $operationKind,
                'amount' => $amount,
                'rawData' => json_encode($csvLine)
            ];

            $csvLines[] = $lineArray;
        }
        return $csvLines;
    }

    /**
     * @param $string
     * @return float
     */
    private function stringToFloat($string): float
    {
        return (float)preg_replace('/[^0-9.]/', '', str_replace(',', '.', $string));
    }

    /**
     * @param DateTime $datetime
     * @param string $realLabel
     * @param float $amount
     * @return string
     */
    private function getOperationUniqId(DateTime $datetime, string $realLabel, float $amount): string
    {
        return md5($datetime->format('d/m/y') . $realLabel . $amount);
    }

    /**
     * @param $bankOperation
     * @param User $user
     * @return BankOperation
     */
    private function createBankOperation($bankOperation, User $user): BankOperation
    {
        $bankOperationItem = new BankOperation();
        $bankOperationItem
            ->setUniqId($bankOperation['uniqId'])
            ->setOperationDate($bankOperation['operationDate'])
            ->setLabel($bankOperation['label'])
            ->setOperationKind($bankOperation['operationKind'])
            ->setAmount($bankOperation['amount'])
            ->setRawData($bankOperation['rawData'])
            ->setUser($user);

        $this->entityManager->persist($bankOperationItem);
        return $bankOperationItem;
    }
}
