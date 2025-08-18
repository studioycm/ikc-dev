# Legacy DB Starter Overview (W2 focus since 2022-03-01)

This overview lists major tables (by W2 row counts) and highlights key columns and relations.

## Shows_Classes

- Approx W2 rows: 303775
- Date filter: created_at
    - Age_FromMonths (Age From Months): 303775/303775 (100%) — A should
    - Age_TillMonths (Age Till Months): 303775/303775 (100%) — A should
    - BreedID (Breed Id): 303775/303775 (100%) — A should; Likely maps to BreedsDB.BreedCode; verify per show.
    - ClassName (Class Name): 303775/303775 (100%) — A should
    - ColorID (Color Id): 303775/303775 (100%) — A should; Likely foreign key; verify relation mapping.
    - CreationDateTime (Creation Date Time): 303775/303775 (100%) — A should; Date/time semantics; check timezone and
      nullability.
    - DataID (Data Id): 303775/303775 (100%) — A should
    - GenderID (Gender Id): 303775/303775 (100%) — A should; Likely foreign key; verify relation mapping.
    - HairID (Hair Id): 303775/303775 (100%) — A should; Likely foreign key; verify relation mapping.
    - IsChampClass (Is Champ Class): 303775/303775 (100%) — A should

## action_logs

- Approx W2 rows: 93292
- Date filter: created_at
    - action_full_desc (Action Full Desc): 93292/93292 (100%) — A should
    - action_name (Action Name): 93292/93292 (100%) — A should
    - action_topic (Action Topic): 93292/93292 (100%) — A should
    - created_at (Created At): 93292/93292 (100%) — A should
    - date_time (Date Time): 93292/93292 (100%) — A should; Date/time semantics; check timezone and nullability.
    - id (Id): 93292/93292 (100%) — A should
    - user_id (User Id): 93292/93292 (100%) — A should
    - user_ip (User Ip): 93292/93292 (100%) — A should
    - current_field (Current Field): 83170/93292 (89.15%) — B should
    - updated_field (Updated Field): 42842/93292 (45.92%) — C consider; Date/time semantics; check timezone and
      nullability.

## public_registration

- Approx W2 rows: 38777
- Date filter: created_at
    - created_at (Created At): 38777/38777 (100%) — A should
    - id (Id): 38777/38777 (100%) — A should
    - topic (Topic): 38777/38777 (100%) — A should
    - updated_at (Updated At): 38777/38777 (100%) — A should; Date/time semantics; check timezone and nullability.
    - mobile_phone (Mobile Phone): 38711/38777 (99.83%) — A should
    - first_name (First Name): 38640/38777 (99.65%) — A should
    - sagirID (Sagir Id): 38605/38777 (99.56%) — A should; Likely foreign key; verify relation mapping.
    - last_name (Last Name): 38601/38777 (99.55%) — A should
    - dog_name (Dog Name): 38579/38777 (99.49%) — A should
    - mobile_prefix (Mobile Prefix): 38514/38777 (99.32%) — A should

## DogsDB

- Approx W2 rows: 29042
- Date filter: created_at
    - DataID (Data Id): 29042/29042 (100%) — A should
    - GenderID (Gender Id): 29042/29042 (100%) — A should; Likely foreign key; verify relation mapping.
    - RaceID (Race Id): 29042/29042 (100%) — A should; Breed mapping via BreedsDB.BreedCode.
    - SagirID (Sagir Id): 29042/29042 (100%) — A should; Likely foreign key; verify relation mapping.
    - created_at (Created At): 29042/29042 (100%) — A should
    - id (Id): 29042/29042 (100%) — A should
    - updated_at (Updated At): 29042/29042 (100%) — A should; Date/time semantics; check timezone and nullability.
    - ColorID (Color Id): 29037/29042 (99.98%) — A should; Likely foreign key; verify relation mapping.
    - sagir_prefix (Sagir Prefix): 29036/29042 (99.98%) — A should
    - HairID (Hair Id): 29028/29042 (99.95%) — A should; Likely foreign key; verify relation mapping.

## dogs2users

- Approx W2 rows: 29002
- Date filter: created_at
    - created_at (Created At): 29002/29002 (100%) — A should
    - id (Id): 29002/29002 (100%) — A should
    - sagir_id (Sagir Id): 29002/29002 (100%) — A should
    - status (Status): 29002/29002 (100%) — A should
    - updated_at (Updated At): 29002/29002 (100%) — A should; Date/time semantics; check timezone and nullability.
    - user_id (User Id): 28917/29002 (99.71%) — A should
    - deleted_at (Deleted At): 2768/29002 (9.54%) — D shouldn't
    - is_current_owner (Is Current Owner): 689/29002 (2.38%) — F shouldn't
    - Show_In_Pedigree (Show In Pedigree): 143/29002 (0.49%) — F shouldn't

## shows_registration

- Approx W2 rows: 27849
- Date filter: created_at
    - CreationDateTime (Creation Date Time): 27849/27849 (100%) — A should; Date/time semantics; check timezone and
      nullability.
    - Owner_FirstName (Owner First Name): 27848/27849 (100%) — A should
    - ShowID (Show Id): 27849/27849 (100%) — A should; Likely foreign key; verify relation mapping.
    - created_at (Created At): 27849/27849 (100%) — A should
    - id (Id): 27849/27849 (100%) — A should
    - updated_at (Updated At): 27849/27849 (100%) — A should; Date/time semantics; check timezone and nullability.
    - Owner_Mobile (Owner Mobile): 27846/27849 (99.99%) — A should
    - Owner_LastName (Owner Last Name): 27844/27849 (99.98%) — A should
    - BirthDate (Birth Date): 27745/27849 (99.63%) — A should; Date/time semantics; check timezone and nullability.
    - BreedID (Breed Id): 27745/27849 (99.63%) — A should; Likely foreign key; verify relation mapping.

## payment

- Approx W2 rows: 25363
- Date filter: created_at
    - amount (Amount): 25363/25363 (100%) — A should; Monetary amount.
    - created_at (Created At): 25363/25363 (100%) — A should
    - desc (Desc): 25363/25363 (100%) — A should
    - id (Id): 25363/25363 (100%) — A should
    - payment_date_time (Payment Date Time): 25363/25363 (100%) — A should; Date/time semantics; check timezone and
      nullability.
    - payment_topic (Payment Topic): 25363/25363 (100%) — A should
    - updated_at (Updated At): 25363/25363 (100%) — A should; Date/time semantics; check timezone and nullability.
    - user_ip (User Ip): 25363/25363 (100%) — A should
    - created_by (Created By): 25226/25363 (99.46%) — A should
    - first_name (First Name): 24154/25363 (95.23%) — A should

## Shows_Dogs_DB

- Approx W2 rows: 23823
- Date filter: created_at
    - BirthDate (Birth Date): 23823/23823 (100%) — A should; Date/time semantics; check timezone and nullability.
    - BreedID (Breed Id): 23823/23823 (100%) — A should; Breed mapping via BreedsDB.BreedCode (non-standard code FK).
      Prefer ShowBreedID → Shows_Breeds.DataID for per-show link when present.
    - CreationDateTime (Creation Date Time): 23823/23823 (100%) — A should; Date/time semantics; check timezone and
      nullability.
    - DataID (Data Id): 23823/23823 (100%) — A should
    - GenderID (Gender Id): 23823/23823 (100%) — A should; Likely foreign key; verify relation mapping.
    - ModificationDateTime (Modification Date Time): 23823/23823 (100%) — A should; Date/time semantics; check timezone
      and nullability.
    - OrderID (Order Id): 23823/23823 (100%) — A should; Likely foreign key; verify relation mapping.
    - SagirID (Sagir Id): 23823/23823 (100%) — A should; Dog primary key (non-standard pk); relates to DogsDB.SagirID.
    - ShowID (Show Id): 23823/23823 (100%) — A should; Likely foreign key; verify relation mapping.
    - ShowRegistrationID (Show Registration Id): 23823/23823 (100%) — A should; Likely foreign key; verify relation
      mapping.

## Dogs_ScoresDB

- Approx W2 rows: 23354
- Date filter: created_at
    - AwardID (Award Id): 23354/23354 (100%) — A should; Likely foreign key; verify relation mapping.
    - DataID (Data Id): 23354/23354 (100%) — A should
    - EventDate (Event Date): 23354/23354 (100%) — A should; Date/time semantics; check timezone and nullability.
    - SagirID (Sagir Id): 23354/23354 (100%) — A should; Likely foreign key; verify relation mapping.
    - created_at (Created At): 23354/23354 (100%) — A should
    - id (Id): 23354/23354 (100%) — A should
    - updated_at (Updated At): 23354/23354 (100%) — A should; Date/time semantics; check timezone and nullability.
    - EventName (Event Name): 23327/23354 (99.88%) — A should
    - JudgeName (Judge Name): 23283/23354 (99.7%) — A should
    - EventPlace (Event Place): 22416/23354 (95.98%) — A should

## shows_results

- Approx W2 rows: 21561
- Date filter: created_at
    - BB (Bb): 21561/21561 (100%) — A should
    - BBIS (Bbis): 21561/21561 (100%) — A should
    - BBIS2 (Bbis2): 21561/21561 (100%) — A should
    - BBIS3 (Bbis3): 21561/21561 (100%) — A should
    - BBaby (Bbaby): 21561/21561 (100%) — A should
    - BBaby2 (Bbaby2): 21561/21561 (100%) — A should
    - BBaby3 (Bbaby3): 21561/21561 (100%) — A should
    - BD (Bd): 21561/21561 (100%) — A should
    - BIG (Big): 21561/21561 (100%) — A should
    - BIG2 (Big2): 21561/21561 (100%) — A should

## shows_payments_info

- Approx W2 rows: 20634
- Date filter: created_at
    - BuyerIP (Buyer Ip): 20634/20634 (100%) — A should
    - CreationDateTime (Creation Date Time): 20634/20634 (100%) — A should; Date/time semantics; check timezone and
      nullability.
    - DataID (Data Id): 20634/20634 (100%) — A should
    - DogID (Dog Id): 20634/20634 (100%) — A should; Likely foreign key; verify relation mapping.
    - Last4Digits (Last4Digits): 20634/20634 (100%) — A should
    - PaymentAmount (Payment Amount): 20634/20634 (100%) — A should; Monetary amount (ILS).
    - PaymentStatus (Payment Status): 20634/20634 (100%) — A should
    - PaymentSubject (Payment Subject): 20634/20634 (100%) — A should
    - RegistrationID (Registration Id): 20634/20634 (100%) — A should; FK to shows_registration.id.
    - SagirID (Sagir Id): 20634/20634 (100%) — A should; Dog key to DogsDB.SagirID.

## users

- Approx W2 rows: 18495
- Date filter: created_at
    - created_at (Created At): 18495/18495 (100%) — A should
    - id (Id): 18495/18495 (100%) — A should
    - is_superadmin (Is Superadmin): 18495/18495 (100%) — A should
    - language_id (Language Id): 18495/18495 (100%) — A should
    - logout (Logout): 18495/18495 (100%) — A should
    - status (Status): 18495/18495 (100%) — A should
    - first_name (First Name): 18449/18495 (99.75%) — A should
    - record_type (Record Type): 18426/18495 (99.63%) — A should
    - country_code (Country Code): 15902/18495 (85.98%) — B should
    - mobile_phone (Mobile Phone): 15891/18495 (85.92%) — B should

## breeding_related_dog

- Approx W2 rows: 14856
- Date filter: created_at
    - breeding_id (Breeding Id): 14856/14856 (100%) — A should
    - created_at (Created At): 14856/14856 (100%) — A should
    - id (Id): 14856/14856 (100%) — A should
    - is_submit (Is Submit): 14856/14856 (100%) — A should
    - mother_sagir_id (Mother Sagir Id): 14856/14856 (100%) — A should
    - updated_at (Updated At): 14856/14856 (100%) — A should; Date/time semantics; check timezone and nullability.
    - color (Color): 14725/14856 (99.12%) — A should
    - gender (Gender): 14702/14856 (98.96%) — A should
    - temparory_name (Temparory Name): 14702/14856 (98.96%) — A should
    - chip_number (Chip Number): 14692/14856 (98.9%) — A should

## Shows_Breeds

- Approx W2 rows: 14107
- Date filter: CreationDateTime
    - ArenaID (Arena Id): 14107/14107 (100%) — A should; Likely foreign key; verify relation mapping.
    - CreationDateTime (Creation Date Time): 14107/14107 (100%) — A should; Date/time semantics; check timezone and
      nullability.
    - DataID (Data Id): 14107/14107 (100%) — A should
    - ModificationDateTime (Modification Date Time): 14107/14107 (100%) — A should; Date/time semantics; check timezone
      and nullability.
    - OrderID (Order Id): 14107/14107 (100%) — A should; Likely foreign key; verify relation mapping.
    - RaceID (Race Id): 14107/14107 (100%) — A should; Breed mapping via BreedsDB.BreedCode (non-standard code FK).
    - ShowID (Show Id): 14107/14107 (100%) — A should; Likely foreign key; verify relation mapping.
    - JudgeID (Judge Id): 4281/14107 (30.35%) — A should; Assigned judge (often empty in W2).
    - MainArenaID (Main Arena Id): 0/14107 (0%) — A should; Likely foreign key; verify relation mapping.
    - Remarks (Remarks): 0/14107 (0%) — F shouldn't

## club2user

- Approx W2 rows: 13414
- Date filter: created_at
    - created_at (Created At): 13414/13414 (100%) — A should
    - forbidden (Forbidden): 13414/13414 (100%) — A should
    - id (Id): 13414/13414 (100%) — A should
    - status (Status): 13414/13414 (100%) — A should
    - updated_at (Updated At): 13414/13414 (100%) — A should; Date/time semantics; check timezone and nullability.
    - user_id (User Id): 13414/13414 (100%) — A should
    - club_id (Club Id): 13408/13414 (99.96%) — A should
    - type (Type): 13405/13414 (99.93%) — A should
    - payment_status (Payment Status): 13236/13414 (98.67%) — A should
    - expire_date (Expire Date): 13064/13414 (97.39%) — A should; Date/time semantics; check timezone and nullability.

## task_related_skills

- Approx W2 rows: 8226
- Date filter: created_at
    - created_at (Created At): 8226/8226 (100%) — A should
    - id (Id): 8226/8226 (100%) — A should
    - is_editable (Is Editable): 8226/8226 (100%) — A should
    - task_id (Task Id): 8226/8226 (100%) — A should
    - updated_at (Updated At): 8226/8226 (100%) — A should; Date/time semantics; check timezone and nullability.
    - user_id (User Id): 7918/8226 (96.26%) — A should
    - skill_id (Skill Id): 305/8226 (3.71%) — A should
    - is_manager (Is Manager): 41/8226 (0.5%) — F shouldn't
    - deleted_at (Deleted At): 0/8226 (0%) — F shouldn't

## dogs_documents

- Approx W2 rows: 6988
- Date filter: created_at
    - created_at (Created At): 6988/6988 (100%) — A should
    - id (Id): 6988/6988 (100%) — A should
    - type (Type): 6988/6988 (100%) — A should
    - updated_at (Updated At): 6988/6988 (100%) — A should; Date/time semantics; check timezone and nullability.
    - SagirID (Sagir Id): 6985/6988 (99.96%) — A should; Likely foreign key; verify relation mapping.
    - TestDate (Test Date): 6902/6988 (98.77%) — A should; Date/time semantics; check timezone and nullability.
    - TestFile (Test File): 6822/6988 (97.62%) — A should
    - Notes (Notes): 877/6988 (12.55%) — D shouldn't
    - is_maag (Is Maag): 304/6988 (4.35%) — F shouldn't
    - maag_date (Maag Date): 304/6988 (4.35%) — F shouldn't; Date/time semantics; check timezone and nullability.

## UserActivities

- Approx W2 rows: 6234
- Date filter: created_at
    - Activity_Desc (Activity Desc): 6234/6234 (100%) — A should
    - Activity_Type (Activity Type): 6234/6234 (100%) — A should
    - CreationDateTime (Creation Date Time): 6234/6234 (100%) — A should; Date/time semantics; check timezone and
      nullability.
    - Is_Payment (Is Payment): 6234/6234 (100%) — A should
    - UserID (User Id): 6234/6234 (100%) — A should; Likely foreign key; verify relation mapping.
    - created_at (Created At): 6234/6234 (100%) — A should
    - id (Id): 6234/6234 (100%) — A should
    - updated_at (Updated At): 6234/6234 (100%) — A should; Date/time semantics; check timezone and nullability.
    - Activity_Log (Activity Log): 6233/6234 (99.98%) — A should
    - UserIP (User Ip): 6233/6234 (99.98%) — A should

## breedings

- Approx W2 rows: 3363
- Date filter: created_at
    - BreddingDate (Bredding Date): 3363/3363 (100%) — A should; Date/time semantics; check timezone and nullability.
    - BreedMismatch (Breed Mismatch): 3363/3363 (100%) — A should
    - Female_Breeding_Not_Approved (Female Breeding Not Approved): 3363/3363 (100%) — A should
    - Female_DNA (Female Dna): 3363/3363 (100%) — A should
    - Foreign_Male_Records (Foreign Male Records): 3363/3363 (100%) — A should
    - Male_Breeding_Not_Approved (Male Breeding Not Approved): 3363/3363 (100%) — A should
    - Male_DNA (Male Dna): 3363/3363 (100%) — A should
    - Male_More_Than_2 (Male More Than 2): 3363/3363 (100%) — A should
    - Male_More_Than_5 (Male More Than 5): 3363/3363 (100%) — A should
    - Rules_IsOwner (Rules Is Owner): 3363/3363 (100%) — A should

## DogsOwners

- Approx W2 rows: 2180
- Date filter: CreationDateTime
    - CreationDateTime (Creation Date Time): 2180/2180 (100%) — A should; Date/time semantics; check timezone and
      nullability.
    - DataID (Data Id): 2180/2180 (100%) — A should
    - ModificationDateTime (Modification Date Time): 2180/2180 (100%) — A should; Date/time semantics; check timezone
      and nullability.
    - OwnerCode (Owner Code): 2180/2180 (100%) — A should
    - SagirOwnerID (Sagir Owner Id): 2180/2180 (100%) — A should; Likely foreign key; verify relation mapping.
    - IsCurrentOwner (Is Current Owner): 2179/2180 (99.95%) — A should
    - OrderID (Order Id): 2179/2180 (99.95%) — A should; Likely foreign key; verify relation mapping.
    - OwnerName (Owner Name): 2177/2180 (99.86%) — A should
    - CityName (City Name): 2163/2180 (99.22%) — A should
    - encoding (Encoding): 2143/2180 (98.3%) — A should

## DogsInfo

- Approx W2 rows: 1911
- Date filter: created_at
    - created_at (Created At): 1911/1911 (100%) — A should
    - dog_import_sagir (Dog Import Sagir): 1911/1911 (100%) — A should
    - dog_mobile_phone_code (Dog Mobile Phone Code): 1911/1911 (100%) — A should
    - dog_mobile_phone_code_2 (Dog Mobile Phone Code 2): 1911/1911 (100%) — A should
    - dog_mobile_phone_code_3 (Dog Mobile Phone Code 3): 1911/1911 (100%) — A should
    - dog_name (Dog Name): 1911/1911 (100%) — A should
    - dog_type (Dog Type): 1911/1911 (100%) — A should
    - id (Id): 1911/1911 (100%) — A should
    - updated_at (Updated At): 1911/1911 (100%) — A should; Date/time semantics; check timezone and nullability.
    - user_id (User Id): 1911/1911 (100%) — A should

## users_tasks

- Approx W2 rows: 1391
- Date filter: created_at
    - created_at (Created At): 1391/1391 (100%) — A should
    - id (Id): 1391/1391 (100%) — A should
    - is_editable (Is Editable): 1391/1391 (100%) — A should
    - status (Status): 1391/1391 (100%) — A should
    - task_name (Task Name): 1391/1391 (100%) — A should
    - updated_at (Updated At): 1391/1391 (100%) — A should; Date/time semantics; check timezone and nullability.
    - read_status (Read Status): 42/1391 (3.02%) — F shouldn't
    - related_breeding_process_id (Related Breeding Process Id): 42/1391 (3.02%) — A should
    - related_to_user_id (Related To User Id): 42/1391 (3.02%) — A should
    - task_type (Task Type): 39/1391 (2.8%) — F shouldn't

## HailCity2

- Approx W2 rows: 1353
- Date filter: (none)
    - City_Name (City Name): 1353/1353 (100%) — A should
    - Code_City (Code City): 1353/1353 (100%) — A should
    - COUNTRY_CODE (Country Code): 0/1353 (0%) — F shouldn't
    - Sync (Sync): 0/1353 (0%) — F shouldn't

## HailCity

- Approx W2 rows: 1352
- Date filter: (none)
    - City_Name (City Name): 1352/1352 (100%) — A should
    - Code_City (Code City): 1352/1352 (100%) — A should
    - COUNTRY_CODE (Country Code): 0/1352 (0%) — F shouldn't
    - Sync (Sync): 0/1352 (0%) — F shouldn't

## Shows_Structure

- Approx W2 rows: 1207
- Date filter: created_at
    - ArenaType (Arena Type): 1207/1207 (100%) — A should; Fully populated since 2022.
    - DataID (Data Id): 1207/1207 (100%) — A should
    - GroupName (Group Name): 1207/1207 (100%) — A should
    - ManagerPass (Manager Pass): 1207/1207 (100%) — A should
    - OrderID (Order Id): 1207/1207 (100%) — A should; Likely foreign key; verify relation mapping.
    - ShowID (Show Id): 1207/1207 (100%) — A should; Likely foreign key; verify relation mapping.
    - created_at (Created At): 1207/1207 (100%) — A should
    - id (Id): 1207/1207 (100%) — A should
    - updated_at (Updated At): 1207/1207 (100%) — A should; Date/time semantics; check timezone and nullability.
    - GroupParentID (Group Parent Id): 1206/1207 (99.92%) — A should; Likely foreign key; verify relation mapping.

## users_skills

- Approx W2 rows: 1190
- Date filter: created_at
    - created_at (Created At): 1190/1190 (100%) — A should
    - id (Id): 1190/1190 (100%) — A should
    - skill_id (Skill Id): 1190/1190 (100%) — A should
    - updated_at (Updated At): 1190/1190 (100%) — A should; Date/time semantics; check timezone and nullability.
    - user_id (User Id): 1190/1190 (100%) — A should
    - deleted_at (Deleted At): 952/1190 (80%) — B should
    - breed_id (Breed Id): 0/1190 (0%) — A should
    - club_id (Club Id): 0/1190 (0%) — A should

## ShowsDB

- Approx W2 rows: 559
- Date filter: created_at
    - Check_all_members (Check All Members): 559/559 (100%) — A should
    - ClubID (Club Id): 559/559 (100%) — A should; Likely foreign key; verify relation mapping.
    - DataID (Data Id): 559/559 (100%) — A should; Legacy identifier, unused post-2022.
    - Dog2Price1 (Dog2Price1): 559/559 (100%) — A should
    - Dog2Price10 (Dog2Price10): 559/559 (100%) — A should
    - Dog2Price2 (Dog2Price2): 559/559 (100%) — A should
    - Dog2Price3 (Dog2Price3): 559/559 (100%) — A should
    - Dog2Price4 (Dog2Price4): 559/559 (100%) — A should
    - Dog2Price5 (Dog2Price5): 559/559 (100%) — A should
    - Dog2Price6 (Dog2Price6): 559/559 (100%) — A should

## breedhouses2users

- Approx W2 rows: 534
- Date filter: created_at
    - breedinghouse_id (Breedinghouse Id): 534/534 (100%) — A should
    - created_at (Created At): 534/534 (100%) — A should
    - id (Id): 534/534 (100%) — A should
    - updated_at (Updated At): 534/534 (100%) — A should; Date/time semantics; check timezone and nullability.
    - user_id (User Id): 534/534 (100%) — A should

## user2breeds

- Approx W2 rows: 434
- Date filter: created_at
    - breed_id (Breed Id): 434/434 (100%) — A should
    - created_at (Created At): 434/434 (100%) — A should
    - id (Id): 434/434 (100%) — A should
    - updated_at (Updated At): 434/434 (100%) — A should; Date/time semantics; check timezone and nullability.
    - user_id (User Id): 434/434 (100%) — A should
    - deleted_at (Deleted At): 147/434 (33.87%) — C consider

## translation

- Approx W2 rows: 289
- Date filter: created_at
    - code_lable (Code Lable): 289/289 (100%) — A should
    - created_at (Created At): 289/289 (100%) — A should
    - english_text (English Text): 289/289 (100%) — A should
    - id (Id): 289/289 (100%) — A should
    - lang_id (Lang Id): 289/289 (100%) — A should
    - module_id (Module Id): 289/289 (100%) — A should
    - translated_text (Translated Text): 289/289 (100%) — A should
    - updated_at (Updated At): 64/289 (22.15%) — D shouldn't; Date/time semantics; check timezone and nullability.
    - deleted_at (Deleted At): 0/289 (0%) — F shouldn't

## health

- Approx W2 rows: 262
- Date filter: created_at
    - DataID (Data Id): 262/262 (100%) — A should
    - SagirID (Sagir Id): 262/262 (100%) — A should; Likely foreign key; verify relation mapping.
    - TestDate (Test Date): 262/262 (100%) — A should; Date/time semantics; check timezone and nullability.
    - created_at (Created At): 262/262 (100%) — A should
    - id (Id): 262/262 (100%) — A should
    - type (Type): 262/262 (100%) — A should
    - updated_at (Updated At): 262/262 (100%) — A should; Date/time semantics; check timezone and nullability.
    - TestFile (Test File): 244/262 (93.13%) — B should
    - Notes (Notes): 43/262 (16.41%) — D shouldn't
    - show_in_paper (Show In Paper): 20/262 (7.63%) — D shouldn't

## agra_cities

- Approx W2 rows: 186
- Date filter: created_at
    - created_at (Created At): 186/186 (100%) — A should
    - id (Id): 186/186 (100%) — A should
    - name (Name): 186/186 (100%) — A should
    - updated_at (Updated At): 186/186 (100%) — A should; Date/time semantics; check timezone and nullability.
    - vet_email (Vet Email): 186/186 (100%) — A should

## owner_files

- Approx W2 rows: 147
- Date filter: created_at
    - created_at (Created At): 147/147 (100%) — A should
    - encrypt_key (Encrypt Key): 147/147 (100%) — A should
    - file (File): 147/147 (100%) — A should
    - file_name (File Name): 147/147 (100%) — A should
    - id (Id): 147/147 (100%) — A should
    - owner_id (Owner Id): 147/147 (100%) — A should
    - updated_at (Updated At): 147/147 (100%) — A should; Date/time semantics; check timezone and nullability.
    - deleted_at (Deleted At): 13/147 (8.84%) — D shouldn't

## user_club_manager

- Approx W2 rows: 136
- Date filter: created_at
    - club_id (Club Id): 136/136 (100%) — A should
    - created_at (Created At): 136/136 (100%) — A should
    - id (Id): 136/136 (100%) — A should
    - updated_at (Updated At): 136/136 (100%) — A should; Date/time semantics; check timezone and nullability.
    - user_id (User Id): 136/136 (100%) — A should

## breedinghouses

- Approx W2 rows: 98
- Date filter: created_at
    - EngName (Eng Name): 98/98 (100%) — A should
    - GidulCode (Gidul Code): 98/98 (100%) — A should
    - HebName (Heb Name): 98/98 (100%) — A should
    - MegadelCode (Megadel Code): 98/98 (100%) — A should
    - created_at (Created At): 98/98 (100%) — A should
    - id (Id): 98/98 (100%) — A should
    - perfect (Perfect): 98/98 (100%) — A should
    - recommended (Recommended): 98/98 (100%) — A should
    - status (Status): 98/98 (100%) — A should
    - updated_at (Updated At): 98/98 (100%) — A should; Date/time semantics; check timezone and nullability.

## JudgesDB

- Approx W2 rows: 93
- Date filter: CreationDateTime
    - BreedID (Breed Id): 93/93 (100%) — A should; Rarely used; not a strict FK.
    - Country (Country): 93/93 (100%) — A should
    - CreationDateTime (Creation Date Time): 93/93 (100%) — A should; Date/time semantics; check timezone and
      nullability.
    - DataID (Data Id): 93/93 (100%) — A should
    - JudgeNameEN (Judge Name En): 93/93 (100%) — A should
    - JudgeNameHE (Judge Name He): 93/93 (100%) — A should
    - ModificationDateTime (Modification Date Time): 9/93 (9.68%) — D shouldn't; Date/time semantics; check timezone and
      nullability.
    - Email (Email): 0/93 (0%) — F shouldn't

## migrations

- Approx W2 rows: 75
- Date filter: (none)
    - batch (Batch): 75/75 (100%) — A should
    - id (Id): 75/75 (100%) — A should
    - migration (Migration): 75/75 (100%) — A should

## dogs_titles_db

- Approx W2 rows: 70
- Date filter: created_at
    - DataID (Data Id): 70/70 (100%) — A should
    - TitleCode (Title Code): 70/70 (100%) — A should
    - TitleName (Title Name): 70/70 (100%) — A should
    - created_at (Created At): 70/70 (100%) — A should
    - id (Id): 70/70 (100%) — A should
    - updated_at (Updated At): 70/70 (100%) — A should; Date/time semantics; check timezone and nullability.
    - Remark (Remark): 66/70 (94.29%) — B should
    - TitleDesc (Title Desc): 60/70 (85.71%) — B should
    - deleted_at (Deleted At): 3/70 (4.29%) — F shouldn't
    - CreationDateTime (Creation Date Time): 0/70 (0%) — F shouldn't; Date/time semantics; check timezone and
      nullability.

## puppie_cards

- Approx W2 rows: 35
- Date filter: created_at
    - breeding_id (Breeding Id): 35/35 (100%) — A should
    - created_at (Created At): 35/35 (100%) — A should
    - file_name (File Name): 35/35 (100%) — A should
    - id (Id): 35/35 (100%) — A should
    - updated_at (Updated At): 35/35 (100%) — A should; Date/time semantics; check timezone and nullability.
    - deleted_at (Deleted At): 0/35 (0%) — F shouldn't

## settings

- Approx W2 rows: 28
- Date filter: created_at
    - created_at (Created At): 28/28 (100%) — A should
    - field (Field): 28/28 (100%) — A should
    - id (Id): 28/28 (100%) — A should
    - setting_type (Setting Type): 28/28 (100%) — A should
    - slug (Slug): 28/28 (100%) — A should
    - updated_at (Updated At): 28/28 (100%) — A should; Date/time semantics; check timezone and nullability.
    - value (Value): 28/28 (100%) — A should
    - deleted_at (Deleted At): 0/28 (0%) — F shouldn't

---

Notes:

- Known non-standard relations: DogsDB.RaceID → BreedsDB.BreedCode; Shows_Dogs_DB.BreedID → BreedsDB.BreedCode;
  Shows_Breeds.RaceID → BreedsDB.BreedCode; shows_results.BreedID → BreedsDB.BreedCode;
  shows_registration.registered_by → users.id.
- If any semantics seem off, please confirm (e.g., Shows_Dogs_DB.OwnerID, results.RegDogID legacy joins).
