<?php

namespace PremiumFastNetwork;

class Scopes
{
    /** See, edit, download, and permanently delete your contacts. */
    const CONTACTS =
        'https://www.googleapis.com/auth/contacts';
    /** See and download your contacts. */
    const CONTACTS_READONLY =
        'https://www.googleapis.com/auth/contacts.readonly';
    /** See your primary Google Account email address. */
    const USERINFO_EMAIL =
        'https://www.googleapis.com/auth/userinfo.email';
    /** See your personal info, including any personal info you've made publicly available. */
    const USERINFO_PROFILE =
        'https://www.googleapis.com/auth/userinfo.profile';
}