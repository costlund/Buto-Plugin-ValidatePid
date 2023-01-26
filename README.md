# Buto-Plugin-ValidatePid
Validate swedish PID (Personal Identification Number).
Last digit must match curren scoop.
Correct format is YYYYMMDD-NNNN (or YYYYMMDDNNNN) where last number is a check value 0-9.
```
19950622-8577
```

## Organisation number
One could also validate as organisation number.
```
9506228577
```

## PHP
Validate in code.
```
wfPlugin::includeonce('validate/pid');
$obj = new PluginValidatePid();
$form = new PluginWfArray();
$form->set('items/pid/label', 'pid');
$form->set('items/pid/post_value', '19950622-8577');
$form->set('items/pid/is_valid', true);
wfHelp::yml_dump($obj->validate_pid('pid', $form->get()));
```

## Form
Form validation.
```
items:
  pid:
    type: varchar
    label: 'PID'
    default: rs:pid
    validator:
      -
        plugin: 'validate/pid'
        method: validate_pid
        data:
```
Set data/delimitator to true. Pid must be in format 199506228577.
```
          skip_delimitator: true
```
Set param organisation to validate as NNNNNNNNNN (ten digits).
```
          organisation: true
```
