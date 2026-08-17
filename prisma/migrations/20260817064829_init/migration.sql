-- CreateTable
CREATE TABLE `users` (
    `id` VARCHAR(191) NOT NULL,
    `email` VARCHAR(191) NOT NULL,
    `emailVerified` DATETIME(3) NULL,
    `name` VARCHAR(191) NULL,
    `image` VARCHAR(191) NULL,
    `role` ENUM('OWNER', 'EDITOR', 'STAFF', 'ADMIN') NOT NULL DEFAULT 'OWNER',
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    UNIQUE INDEX `users_email_key`(`email`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `accounts` (
    `id` VARCHAR(191) NOT NULL,
    `userId` VARCHAR(191) NOT NULL,
    `type` VARCHAR(191) NOT NULL,
    `provider` VARCHAR(191) NOT NULL,
    `providerAccountId` VARCHAR(191) NOT NULL,
    `refresh_token` TEXT NULL,
    `access_token` TEXT NULL,
    `expires_at` INTEGER NULL,
    `token_type` VARCHAR(191) NULL,
    `scope` VARCHAR(191) NULL,
    `id_token` TEXT NULL,
    `session_state` VARCHAR(191) NULL,

    INDEX `accounts_userId_idx`(`userId`),
    UNIQUE INDEX `accounts_provider_providerAccountId_key`(`provider`, `providerAccountId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `sessions` (
    `id` VARCHAR(191) NOT NULL,
    `sessionToken` VARCHAR(191) NOT NULL,
    `userId` VARCHAR(191) NOT NULL,
    `expires` DATETIME(3) NOT NULL,

    UNIQUE INDEX `sessions_sessionToken_key`(`sessionToken`),
    INDEX `sessions_userId_idx`(`userId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `verification_tokens` (
    `identifier` VARCHAR(191) NOT NULL,
    `token` VARCHAR(191) NOT NULL,
    `expires` DATETIME(3) NOT NULL,

    UNIQUE INDEX `verification_tokens_token_key`(`token`),
    UNIQUE INDEX `verification_tokens_identifier_token_key`(`identifier`, `token`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `business_profiles` (
    `id` VARCHAR(191) NOT NULL,
    `canonicalId` VARCHAR(191) NOT NULL,
    `slug` VARCHAR(191) NOT NULL,
    `name` VARCHAR(191) NOT NULL,
    `legalName` VARCHAR(191) NULL,
    `industry` ENUM('HEALTHCARE', 'HOSPITALITY', 'RETAIL', 'FOOD', 'FINANCIAL_SERVICES', 'OTHER') NOT NULL,
    `category` VARCHAR(191) NOT NULL,
    `countryCode` VARCHAR(191) NOT NULL,
    `city` VARCHAR(191) NOT NULL,
    `addressLine1` VARCHAR(191) NOT NULL,
    `addressLine2` VARCHAR(191) NULL,
    `postalCode` VARCHAR(191) NULL,
    `lat` DOUBLE NULL,
    `lng` DOUBLE NULL,
    `phone` VARCHAR(191) NULL,
    `publicEmail` VARCHAR(191) NULL,
    `website` VARCHAR(191) NULL,
    `descriptionShort` TEXT NOT NULL,
    `descriptionLong` TEXT NULL,
    `hours` JSON NULL,
    `priceRange` VARCHAR(191) NULL,
    `status` ENUM('DRAFT', 'PUBLISHED', 'SUSPENDED', 'REMOVED') NOT NULL DEFAULT 'DRAFT',
    `claimStatus` ENUM('UNCLAIMED', 'PENDING_CLAIM', 'CLAIMED', 'VERIFIED') NOT NULL DEFAULT 'UNCLAIMED',
    `planTier` ENUM('NONE', 'REGISTERED', 'MANAGED') NOT NULL DEFAULT 'NONE',
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,
    `lastVerifiedAt` DATETIME(3) NULL,
    `publishedAt` DATETIME(3) NULL,

    UNIQUE INDEX `business_profiles_canonicalId_key`(`canonicalId`),
    UNIQUE INDEX `business_profiles_slug_key`(`slug`),
    INDEX `business_profiles_countryCode_idx`(`countryCode`),
    INDEX `business_profiles_industry_idx`(`industry`),
    INDEX `business_profiles_status_idx`(`status`),
    INDEX `business_profiles_claimStatus_idx`(`claimStatus`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `profile_services` (
    `id` VARCHAR(191) NOT NULL,
    `profileId` VARCHAR(191) NOT NULL,
    `name` VARCHAR(191) NOT NULL,
    `description` TEXT NULL,
    `price` VARCHAR(191) NULL,
    `sortOrder` INTEGER NOT NULL DEFAULT 0,

    INDEX `profile_services_profileId_idx`(`profileId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `profile_images` (
    `id` VARCHAR(191) NOT NULL,
    `profileId` VARCHAR(191) NOT NULL,
    `url` VARCHAR(191) NOT NULL,
    `altText` VARCHAR(191) NULL,
    `sortOrder` INTEGER NOT NULL DEFAULT 0,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    INDEX `profile_images_profileId_idx`(`profileId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `profile_ownerships` (
    `id` VARCHAR(191) NOT NULL,
    `userId` VARCHAR(191) NOT NULL,
    `profileId` VARCHAR(191) NOT NULL,
    `role` ENUM('OWNER', 'EDITOR', 'STAFF', 'ADMIN') NOT NULL DEFAULT 'OWNER',
    `grantedAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `grantedViaClaimRequestId` VARCHAR(191) NULL,

    INDEX `profile_ownerships_profileId_idx`(`profileId`),
    UNIQUE INDEX `profile_ownerships_userId_profileId_key`(`userId`, `profileId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `data_sources` (
    `id` VARCHAR(191) NOT NULL,
    `profileId` VARCHAR(191) NOT NULL,
    `sourceType` ENUM('FACEBOOK', 'OTA_BOOKING', 'OTA_AGODA', 'OTA_TRIPADVISOR', 'OWN_WEBSITE', 'REGISTRY', 'GBP_ADJACENT', 'OWNER_SUBMITTED') NOT NULL,
    `sourceUrl` VARCHAR(191) NULL,
    `contactEmail` VARCHAR(191) NULL,
    `contactPhone` VARCHAR(191) NULL,
    `lastCheckedAt` DATETIME(3) NULL,
    `currentSnapshot` JSON NOT NULL,
    `coherenceStatus` ENUM('ALIGNED', 'MINOR_DRIFT', 'MAJOR_DRIFT', 'UNREACHABLE', 'NOT_CHECKED') NOT NULL DEFAULT 'NOT_CHECKED',
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    INDEX `data_sources_profileId_idx`(`profileId`),
    INDEX `data_sources_sourceType_idx`(`sourceType`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `claim_requests` (
    `id` VARCHAR(191) NOT NULL,
    `profileId` VARCHAR(191) NOT NULL,
    `requestedByUserId` VARCHAR(191) NOT NULL,
    `verificationMethod` ENUM('EMAIL_MATCH', 'PHONE_MATCH', 'DOCUMENT_UPLOAD', 'ADMIN_CODE') NOT NULL,
    `contactValue` VARCHAR(191) NOT NULL,
    `status` ENUM('SUBMITTED', 'AWAITING_VERIFICATION', 'VERIFIED', 'APPROVED', 'REJECTED') NOT NULL DEFAULT 'SUBMITTED',
    `otpHash` VARCHAR(191) NULL,
    `otpExpiresAt` DATETIME(3) NULL,
    `documentUrl` VARCHAR(191) NULL,
    `reviewedByStaffId` VARCHAR(191) NULL,
    `reviewNotes` TEXT NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    INDEX `claim_requests_profileId_idx`(`profileId`),
    INDEX `claim_requests_status_idx`(`status`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `claim_audit_events` (
    `id` VARCHAR(191) NOT NULL,
    `claimRequestId` VARCHAR(191) NOT NULL,
    `actorUserId` VARCHAR(191) NULL,
    `action` VARCHAR(191) NOT NULL,
    `metadata` JSON NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    INDEX `claim_audit_events_claimRequestId_idx`(`claimRequestId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `subscriptions` (
    `id` VARCHAR(191) NOT NULL,
    `profileId` VARCHAR(191) NOT NULL,
    `tier` ENUM('NONE', 'REGISTERED', 'MANAGED') NOT NULL,
    `billingCycle` ENUM('ANNUAL') NOT NULL DEFAULT 'ANNUAL',
    `status` ENUM('ACTIVE', 'PAST_DUE', 'CANCELED', 'EXPIRED') NOT NULL,
    `stripeCustomerId` VARCHAR(191) NULL,
    `stripeSubscriptionId` VARCHAR(191) NULL,
    `currentPeriodStart` DATETIME(3) NULL,
    `currentPeriodEnd` DATETIME(3) NULL,
    `renewalDate` DATETIME(3) NULL,
    `canceledAt` DATETIME(3) NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `updatedAt` DATETIME(3) NOT NULL,

    UNIQUE INDEX `subscriptions_profileId_key`(`profileId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `invoices` (
    `id` VARCHAR(191) NOT NULL,
    `subscriptionId` VARCHAR(191) NOT NULL,
    `stripeInvoiceId` VARCHAR(191) NOT NULL,
    `amountCents` INTEGER NOT NULL,
    `currency` VARCHAR(191) NOT NULL DEFAULT 'usd',
    `status` ENUM('PAID', 'OPEN', 'VOID', 'UNCOLLECTIBLE') NOT NULL,
    `issuedAt` DATETIME(3) NOT NULL,
    `pdfUrl` VARCHAR(191) NULL,

    UNIQUE INDEX `invoices_stripeInvoiceId_key`(`stripeInvoiceId`),
    INDEX `invoices_subscriptionId_idx`(`subscriptionId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `freshness_check_logs` (
    `id` VARCHAR(191) NOT NULL,
    `profileId` VARCHAR(191) NOT NULL,
    `dataSourceId` VARCHAR(191) NULL,
    `checkedAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `discrepancies` JSON NOT NULL,
    `severity` ENUM('LOW', 'MEDIUM', 'HIGH') NOT NULL,
    `alertSent` BOOLEAN NOT NULL DEFAULT false,
    `alertSentAt` DATETIME(3) NULL,
    `resolvedAt` DATETIME(3) NULL,
    `resolutionAction` ENUM('ACCEPTED_NEW_VALUE', 'KEPT_CURRENT_VALUE') NULL,

    INDEX `freshness_check_logs_profileId_idx`(`profileId`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `crawler_visit_logs` (
    `id` VARCHAR(191) NOT NULL,
    `profileId` VARCHAR(191) NOT NULL,
    `botName` ENUM('GPTBOT', 'OAI_SEARCHBOT', 'CLAUDEBOT', 'PERPLEXITYBOT', 'GOOGLE_EXTENDED', 'GOOGLEBOT', 'BINGBOT', 'OTHER') NOT NULL,
    `path` VARCHAR(191) NOT NULL,
    `userAgent` TEXT NOT NULL,
    `ipHash` VARCHAR(191) NOT NULL,
    `timestamp` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),

    INDEX `crawler_visit_logs_profileId_timestamp_idx`(`profileId`, `timestamp`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `crawler_visit_daily_aggs` (
    `id` VARCHAR(191) NOT NULL,
    `profileId` VARCHAR(191) NOT NULL,
    `date` DATE NOT NULL,
    `botName` ENUM('GPTBOT', 'OAI_SEARCHBOT', 'CLAUDEBOT', 'PERPLEXITYBOT', 'GOOGLE_EXTENDED', 'GOOGLEBOT', 'BINGBOT', 'OTHER') NOT NULL,
    `visitCount` INTEGER NOT NULL DEFAULT 0,

    INDEX `crawler_visit_daily_aggs_profileId_idx`(`profileId`),
    UNIQUE INDEX `crawler_visit_daily_aggs_profileId_date_botName_key`(`profileId`, `date`, `botName`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `disputes` (
    `id` VARCHAR(191) NOT NULL,
    `profileId` VARCHAR(191) NOT NULL,
    `submittedByUserId` VARCHAR(191) NULL,
    `submitterEmail` VARCHAR(191) NOT NULL,
    `type` ENUM('NOT_MY_BUSINESS', 'INCORRECT_DATA', 'DUPLICATE', 'UNWANTED_LISTING', 'OTHER') NOT NULL,
    `description` TEXT NOT NULL,
    `status` ENUM('OPEN', 'IN_REVIEW', 'CORRECTED', 'REMOVED', 'REJECTED') NOT NULL DEFAULT 'OPEN',
    `resolutionNotes` TEXT NULL,
    `resolvedByStaffId` VARCHAR(191) NULL,
    `createdAt` DATETIME(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
    `resolvedAt` DATETIME(3) NULL,

    INDEX `disputes_profileId_idx`(`profileId`),
    INDEX `disputes_status_idx`(`status`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `country_clearances` (
    `id` VARCHAR(191) NOT NULL,
    `countryCode` VARCHAR(191) NOT NULL,
    `countryName` VARCHAR(191) NOT NULL,
    `legalStatus` ENUM('NOT_STARTED', 'IN_REVIEW', 'CLEARED', 'EXCLUDED_GDPR') NOT NULL DEFAULT 'NOT_STARTED',
    `gdprExcluded` BOOLEAN NOT NULL DEFAULT false,
    `notes` TEXT NULL,
    `clearedAt` DATETIME(3) NULL,
    `reviewedByStaffId` VARCHAR(191) NULL,
    `updatedAt` DATETIME(3) NOT NULL,

    UNIQUE INDEX `country_clearances_countryCode_key`(`countryCode`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- CreateTable
CREATE TABLE `ingestion_source_statuses` (
    `id` VARCHAR(191) NOT NULL,
    `countryCode` VARCHAR(191) NOT NULL,
    `sourceType` ENUM('FACEBOOK', 'OTA_BOOKING', 'OTA_AGODA', 'OTA_TRIPADVISOR', 'OWN_WEBSITE', 'REGISTRY', 'GBP_ADJACENT', 'OWNER_SUBMITTED') NOT NULL,
    `lastRunAt` DATETIME(3) NULL,
    `lastRunStatus` ENUM('SUCCESS', 'PARTIAL', 'FAILED') NULL,
    `recordsIngested` INTEGER NOT NULL DEFAULT 0,
    `recordsFailed` INTEGER NOT NULL DEFAULT 0,
    `nextScheduledRun` DATETIME(3) NULL,

    UNIQUE INDEX `ingestion_source_statuses_countryCode_sourceType_key`(`countryCode`, `sourceType`),
    PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- AddForeignKey
ALTER TABLE `accounts` ADD CONSTRAINT `accounts_userId_fkey` FOREIGN KEY (`userId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `sessions` ADD CONSTRAINT `sessions_userId_fkey` FOREIGN KEY (`userId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `business_profiles` ADD CONSTRAINT `business_profiles_countryCode_fkey` FOREIGN KEY (`countryCode`) REFERENCES `country_clearances`(`countryCode`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `profile_services` ADD CONSTRAINT `profile_services_profileId_fkey` FOREIGN KEY (`profileId`) REFERENCES `business_profiles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `profile_images` ADD CONSTRAINT `profile_images_profileId_fkey` FOREIGN KEY (`profileId`) REFERENCES `business_profiles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `profile_ownerships` ADD CONSTRAINT `profile_ownerships_userId_fkey` FOREIGN KEY (`userId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `profile_ownerships` ADD CONSTRAINT `profile_ownerships_profileId_fkey` FOREIGN KEY (`profileId`) REFERENCES `business_profiles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `data_sources` ADD CONSTRAINT `data_sources_profileId_fkey` FOREIGN KEY (`profileId`) REFERENCES `business_profiles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `claim_requests` ADD CONSTRAINT `claim_requests_profileId_fkey` FOREIGN KEY (`profileId`) REFERENCES `business_profiles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `claim_requests` ADD CONSTRAINT `claim_requests_requestedByUserId_fkey` FOREIGN KEY (`requestedByUserId`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `claim_requests` ADD CONSTRAINT `claim_requests_reviewedByStaffId_fkey` FOREIGN KEY (`reviewedByStaffId`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `claim_audit_events` ADD CONSTRAINT `claim_audit_events_claimRequestId_fkey` FOREIGN KEY (`claimRequestId`) REFERENCES `claim_requests`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `claim_audit_events` ADD CONSTRAINT `claim_audit_events_actorUserId_fkey` FOREIGN KEY (`actorUserId`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `subscriptions` ADD CONSTRAINT `subscriptions_profileId_fkey` FOREIGN KEY (`profileId`) REFERENCES `business_profiles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `invoices` ADD CONSTRAINT `invoices_subscriptionId_fkey` FOREIGN KEY (`subscriptionId`) REFERENCES `subscriptions`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `freshness_check_logs` ADD CONSTRAINT `freshness_check_logs_profileId_fkey` FOREIGN KEY (`profileId`) REFERENCES `business_profiles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `freshness_check_logs` ADD CONSTRAINT `freshness_check_logs_dataSourceId_fkey` FOREIGN KEY (`dataSourceId`) REFERENCES `data_sources`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `crawler_visit_logs` ADD CONSTRAINT `crawler_visit_logs_profileId_fkey` FOREIGN KEY (`profileId`) REFERENCES `business_profiles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `crawler_visit_daily_aggs` ADD CONSTRAINT `crawler_visit_daily_aggs_profileId_fkey` FOREIGN KEY (`profileId`) REFERENCES `business_profiles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `disputes` ADD CONSTRAINT `disputes_profileId_fkey` FOREIGN KEY (`profileId`) REFERENCES `business_profiles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `disputes` ADD CONSTRAINT `disputes_submittedByUserId_fkey` FOREIGN KEY (`submittedByUserId`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `disputes` ADD CONSTRAINT `disputes_resolvedByStaffId_fkey` FOREIGN KEY (`resolvedByStaffId`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE `country_clearances` ADD CONSTRAINT `country_clearances_reviewedByStaffId_fkey` FOREIGN KEY (`reviewedByStaffId`) REFERENCES `users`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;
