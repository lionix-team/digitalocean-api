<?php

declare(strict_types=1);

namespace Digitalocean\Enums;

/**
 * @see https://docs.digitalocean.com/reference/api/digitalocean/#tag/Droplet-Actions
 */
enum DropletActionType: string
{
    case EnableBackups = 'enable_backups';
    case DisableBackups = 'disable_backups';
    case ChangeBackupPolicy = 'change_backup_policy';
    case Reboot = 'reboot';
    case PowerCycle = 'power_cycle';
    case Shutdown = 'shutdown';
    case PowerOff = 'power_off';
    case PowerOn = 'power_on';
    case Restore = 'restore';
    case PasswordReset = 'password_reset';
    case Resize = 'resize';
    case Rebuild = 'rebuild';
    case Rename = 'rename';
    case ChangeKernel = 'change_kernel';
    case EnableIpv6 = 'enable_ipv6';
    case Snapshot = 'snapshot';
}
