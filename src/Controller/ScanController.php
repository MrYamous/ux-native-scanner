<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Event;
use App\Form\ContactType;
use App\Form\ScanType;
use App\Ocr\OcrClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ScanController extends AbstractController
{
    #[Route('/scan', name: 'app_scan', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        OcrClientInterface $ocrClient,
        EntityManagerInterface $entityManager
    ): Response {
        $scanForm = $this->createForm(ScanType::class);
        $scanForm->handleRequest($request);

        if ($scanForm->isSubmitted() && $scanForm->isValid()) {
            /** @var UploadedFile $file */
            $file = $scanForm->get('image')->getData();
            /** @var Event|null $selectedEvent */
            $selectedEvent = $scanForm->get('event')->getData();

            try {
                $ocrData = $ocrClient->extractContactData($file);

                $contact = new Contact();
                $contact->setFirstname($ocrData->firstname);
                $contact->setLastname($ocrData->lastname);
                $contact->setEmail($ocrData->email);
                $contact->setPhone($ocrData->phone);
                $contact->setCompany($ocrData->company);
                $contact->setWebsite($ocrData->website);
                $contact->setAddress($ocrData->address);
                if ($ocrData->rawText) {
                    $contact->setNotes("Texte extrait par OCR :\n" . $ocrData->rawText);
                }

                if ($selectedEvent) {
                    $contact->addEvent($selectedEvent);
                }

                $contactForm = $this->createForm(ContactType::class, $contact, [
                    'action' => $this->generateUrl('app_scan_save'),
                ]);

                return $this->render('scan/review.html.twig', [
                    'contactForm' => $contactForm->createView(),
                    'ocrData' => $ocrData,
                ]);
            } catch (\Throwable $e) {
                $this->addFlash('danger', 'Erreur lors du traitement OCR : ' . $e->getMessage());
            }
        }

        return $this->render('scan/index.html.twig', [
            'scanForm' => $scanForm->createView(),
        ]);
    }

    #[Route('/scan/save', name: 'app_scan_save', methods: ['POST'])]
    public function save(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contact);
            $entityManager->flush();

            $this->addFlash('success', 'Le contact a été créé avec succès suite au scan !');

            return $this->redirectToRoute('app_contact_show', ['id' => $contact->getId()]);
        }

        return $this->render('scan/review.html.twig', [
            'contactForm' => $form->createView(),
            'ocrData' => null,
        ]);
    }
}
