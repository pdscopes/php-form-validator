<?php

return function (\MadeSimple\Validator\Validator $validator) {
    $validator
        ->setRuleMessage('present', ':attribute muss vorhanden sein')
        ->setRuleMessage('required', ':attribute !ist|sind erforderlich')
        ->setRuleMessage('required-if', ':attribute !ist|sind erforderlich wenn :field gleich %value')
        ->setRuleMessage('required-with', ':attribute !ist|sind erforderlich wenn :field vorhanden ist')
        ->setRuleMessage('required-with-all', ':attribute !ist|sind erforderlich')
        ->setRuleMessage('required-with-any', ':attribute !ist|sind erforderlich')
        ->setRuleMessage('required-without', ':attribute !ist|sind erforderlich wenn :field nicht vorhanden ist')

        ->setRuleMessage('equals', ':attribute muss gleich :field sein')
        ->setRuleMessage('not-equals', ':attribute darf nicht gleich :field sein')
        ->setRuleMessage('identical', ':attribute muss identisch sein mit :field')
        ->setRuleMessage('not-identical', ':attribute darf nicht identisch sein mit :field')

        ->setRuleMessage('in', ':attribute muss eins der folgenden sein: %values')
        ->setRuleMessage('not-in', ':attribute darf nicht eins der folgenden sein: %values')
        ->setRuleMessage('contains', ':attribute muss %values enthalten')
        ->setRuleMessage('contains-only', ':attribute darf nur %values enthalten')
        ->setRuleMessage('min-arr-count', ':attribute muss mindestens :min Element(e) enthalten')
        ->setRuleMessage('max-arr-count', ':attribute darf höchstens :max Element(e) enthalten')

        ->setRuleMessage('min', ':attribute muss mindestens :min sein')
        ->setRuleMessage('max', ':attribute darf höchstens :max sein')
        ->setRuleMessage('greater-than', ':attribute muss größer als :field sein')
        ->setRuleMessage('less-than', ':attribute muss kleiner als :field sein')

        ->setRuleMessage('alpha', ':attribute darf nur alphabetische Zeichen enthalten')
        ->setRuleMessage('alpha-numeric', ':attribute darf nur alpha-nummerische Zeichen enthalten')
        ->setRuleMessage('min-str-len', ':attribute muss mindestens :min Zeichen lang sein')
        ->setRuleMessage('max-str-len', ':attribute darf höchstens :max Zeichen lang sein')
        ->setRuleMessage('str-len', ':attribute muss genau %value Zeichen lang sein')
        ->setRuleMessage('human-name', ':attribute muss ein gültiger Name sein')

        ->setRuleMessage('is', ':attribute muss vom Typ :type sein')

        ->setRuleMessage('email', ':attribute muss eine gültige Emailadresse')
        ->setRuleMessage('date', ':attribute muss ein Datum in folgendem Format sein: :format')
        ->setRuleMessage('url', ':attribute muss eine gültige URL sein')
        ->setRuleMessage('uuid', ':attribute muss eine gültige UUID sein')

        ->setRuleMessage('card-number', ':attribute muss eine gültige Kreditkartennummer sein');
};
