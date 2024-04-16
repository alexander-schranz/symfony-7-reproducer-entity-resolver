# Reproducer Symfony 7 cache clear issue

Dependencies:

```bash
composer create-project symfony/skeleton doctrine-entity-resolver-reproducer
cd  doctrine-entity-resolver-reproducer

composer require orm
composer require twig
composer require debug
```

Changes:

```diff
diff --git a/src/Entity/Some.php b/src/Entity/Some.php
new file mode 100644
index 0000000..692bc41
--- /dev/null
+++ b/src/Entity/Some.php
@@ -0,0 +1,22 @@
+<?php
+
+namespace App\Entity;
+
+use Doctrine\ORM\Mapping as ORM;
+
+#[ORM\Entity()]
+class Some implements SomeInterface
+{
+    #[ORM\Id]
+    #[ORM\GeneratedValue]
+    #[ORM\Column(type: 'integer')]
+    private $id;
+
+    #[ORM\Column(type: 'string')]
+    private $title;
+
+    public function getTitle(): string
+    {
+        return $this->title;
+    }
+}
diff --git a/src/Entity/SomeInterface.php b/src/Entity/SomeInterface.php
new file mode 100644
index 0000000..185d66c
--- /dev/null
+++ b/src/Entity/SomeInterface.php
@@ -0,0 +1,8 @@
+<?php
+
+namespace App\Entity;
+
+interface SomeInterface
+{
+    public function getTitle(): string;
+}
diff --git a/src/Twig/SomeExtension.php b/src/Twig/SomeExtension.php
new file mode 100644
index 0000000..2086521
--- /dev/null
+++ b/src/Twig/SomeExtension.php
@@ -0,0 +1,17 @@
+<?php
+
+namespace App\Twig;
+
+use App\Entity\SomeInterface;
+use Doctrine\ORM\EntityManagerInterface;
+use Doctrine\ORM\EntityRepository;
+
+class SomeExtension extends \Twig\Extension\AbstractExtension
+{
+    private EntityRepository $someRepository;
+
+    public function __construct(EntityManagerInterface $entityManager)
+    {
+         $this->someRepository = $entityManager->getRepository(SomeInterface::class);
+    }
+}
```

Running:

```
bin/console cache:clear -vvv

In MappingException.php line 80:

  [Doctrine\Persistence\Mapping\MappingException]
  Class 'App\Entity\SomeInterface' does not exist


Exception trace:
  at /project/vendor/doctrine/persistence/src/Persistence/Mapping/MappingException.php:80
 Doctrine\Persistence\Mapping\MappingException::nonExistingClass() at /project/vendor/doctrine/persistence/src/Persistence/Mapping/RuntimeReflectionService.php:39
 Doctrine\Persistence\Mapping\RuntimeReflectionService->getParentClasses() at /project/vendor/doctrine/persistence/src/Persistence/Mapping/AbstractClassMetadataFactory.php:283
 Doctrine\Persistence\Mapping\AbstractClassMetadataFactory->getParentClasses() at /project/vendor/doctrine/persistence/src/Persistence/Mapping/AbstractClassMetadataFactory.php:318
 Doctrine\Persistence\Mapping\AbstractClassMetadataFactory->loadMetadata() at /project/vendor/doctrine/persistence/src/Persistence/Mapping/AbstractClassMetadataFactory.php:207
 Doctrine\Persistence\Mapping\AbstractClassMetadataFactory->getMetadataFor() at /project/vendor/doctrine/orm/src/EntityManager.php:215
 Doctrine\ORM\EntityManager->getClassMetadata() at /project/vendor/doctrine/doctrine-bundle/src/Repository/ContainerRepositoryFactory.php:49
 Doctrine\Bundle\DoctrineBundle\Repository\ContainerRepositoryFactory->doGetRepository() at /project/vendor/doctrine/doctrine-bundle/src/Repository/RepositoryFactoryCompatibility.php:27
 Doctrine\Bundle\DoctrineBundle\Repository\ContainerRepositoryFactory->getRepository() at /project/vendor/doctrine/orm/src/EntityManager.php:490
 Doctrine\ORM\EntityManager->getRepository() at /project/src/Twig/SomeExtension.php:15
 App\Twig\SomeExtension->__construct() at /project/var/cache/dev/ContainerUPDBNXK/App_KernelDevDebugContainer.php:998
 ContainerUPDBNXK\App_KernelDevDebugContainer::getTwigService() at /project/var/cache/dev/ContainerUPDBNXK/App_KernelDevDebugContainer.php:349
 ContainerUPDBNXK\App_KernelDevDebugContainer::get_Container_Private_ProfilerService() at /project/var/cache/dev/ContainerUPDBNXK/getConsoleProfilerListenerService.php:23
 ContainerUPDBNXK\getConsoleProfilerListenerService::do() at /project/var/cache/dev/ContainerUPDBNXK/App_KernelDevDebugContainer.php:306
 ContainerUPDBNXK\App_KernelDevDebugContainer->load() at /project/var/cache/dev/ContainerUPDBNXK/App_KernelDevDebugContainer.php:576
 ContainerUPDBNXK\App_KernelDevDebugContainer::ContainerUPDBNXK\{closure}() at /project/vendor/symfony/event-dispatcher/EventDispatcher.php:221
 Symfony\Component\EventDispatcher\EventDispatcher->sortListeners() at /project/vendor/symfony/event-dispatcher/EventDispatcher.php:70
 Symfony\Component\EventDispatcher\EventDispatcher->getListeners() at /project/vendor/symfony/event-dispatcher/Debug/TraceableEventDispatcher.php:257
 Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher->preProcess() at /project/vendor/symfony/event-dispatcher/Debug/TraceableEventDispatcher.php:121
 Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher->dispatch() at /project/vendor/symfony/console/Application.php:1046
 Symfony\Component\Console\Application->doRunCommand() at /project/vendor/symfony/framework-bundle/Console/Application.php:125
 Symfony\Bundle\FrameworkBundle\Console\Application->doRunCommand() at /project/vendor/symfony/console/Application.php:318
 Symfony\Component\Console\Application->doRun() at /project/vendor/symfony/framework-bundle/Console/Application.php:79
 Symfony\Bundle\FrameworkBundle\Console\Application->doRun() at /project/vendor/symfony/console/Application.php:169
 Symfony\Component\Console\Application->run() at /project/vendor/symfony/runtime/Runner/Symfony/ConsoleApplicationRunner.php:49
 Symfony\Component\Runtime\Runner\Symfony\ConsoleApplicationRunner->run() at /project/vendor/autoload_runtime.php:29
 require_once() at /project/bin/console:15

cache:clear [--no-warmup] [--no-optional-warmers]
```
